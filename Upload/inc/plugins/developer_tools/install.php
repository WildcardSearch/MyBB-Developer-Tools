<?php
/*
 * Plugin Name: Developer Tools for MyBB 1.8.x
 * Copyright 2018 WildcardSearch
 * http://www.rantcentralforums.com
 *
 * This file contains the install functions for acp.php
 */

// disallow direct access to this file for security reasons
if (!defined('IN_MYBB')) {
	die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');
}

/**
 * used by MyBB to provide relevant information about the plugin and
 * also link users to updates
 *
 * @return array plugin info
 */
function developer_tools_info()
{
	global $lang, $cp_style;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	$settingsLink = developerToolsBuildSettingsLink();

	if ($settingsLink) {
		$settingsLink = <<<EOF
				<li style="list-style-image: url(styles/{$cp_style}/images/developer_tools/settings.gif); margin-top: 10px;">
					{$settingsLink}
				</li>
EOF;

		$buttonPic = "styles/{$cp_style}/images/developer_tools/donate.gif";
		$borderPic = "styles/{$cp_style}/images/developer_tools/pixel.gif";
		$developerToolsDescription = <<<EOF

<table style="width: 100%;">
	<tr>
		<td style="width: 75%;">
			{$lang->developer_tools_description}
			<ul id="mm_options">
{$settingsLink}
			</ul>
		</td>
		<td style="text-align: center;">
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="VA5RFLBUC4XM4">
				<input type="image" src="{$buttonPic}" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="{$borderPic}" width="1" height="1">
			</form>
		</td>
	</tr>
</table>
EOF;
	} else {
		$developerToolsDescription = $lang->developer_tools_description;
	}

	$developerToolsDescription .= developerToolsCheckRequirements();

	$name = <<<EOF
<span style="font-familiy: arial; font-size: 1.5em; color: white; text-shadow: 2px 2px 2px lightgrey; background: black; border-radius: 3px; box-shadow: 1px 1px 2px white; display: inline-block; padding: 0px 10px 3px 10px;">{$lang->developer_tools}</span>
EOF;
	$author = <<<EOF
</a></small></i><a href="http://www.rantcentralforums.com" title="Rant Central"><span style="font-family: Courier New; font-weight: bold; font-size: 1.2em; color: #117eec;">Wildcard</span></a><i><small><a>
EOF;

    // return the info
	return array(
        'name' => $name,
        'description' => $developerToolsDescription,
        'version' => DEVELOPER_TOOLS_VERSION,
        'author' => $author,
        'authorsite' => 'http://www.rantcentralforums.com/',
		'compatibility' => '18*',
		'codename' => 'developer_tools',
    );
}

/**
 * check to see if the plugin is installed
 *
 * @return bool true if installed, false if not
 */
function developer_tools_is_installed()
{
	return developerToolsGetSettingsgroup();
}

/**
 *
 *
 * @return void
 */
function developer_tools_install()
{
	global $lang;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	$req = developerToolsCheckRequirements(true);
	if ($req) {
		flash_message("{$lang->developer_tools_cannot_be_installed}<br /><br />{$req}", 'error');
		admin_redirect('index.php?module=config-plugins');
	}

	DeveloperToolsInstaller::getInstance()->install();
}

/**
 * version check
 *
 * @return void
 */
function developer_tools_activate()
{
	$myCache = DeveloperToolsCache::getInstance();

	$oldVersion = $myCache->getVersion();
	if (version_compare($oldVersion, DEVELOPER_TOOLS_VERSION, '<') &&
		$oldVersion != '' &&
		$oldVersion != 0) {

		// check everything and upgrade if necessary
		developer_tools_install();
    }

	// update the version (so we don't try to upgrade next round)
	$myCache->setVersion(DEVELOPER_TOOLS_VERSION);

	change_admin_permission('developer_tools', false);
	foreach ((array) developerToolsGetAllModules() as $key => $module) {
		change_admin_permission('developer_tools', $key);
	}
}

/**
 * permissions
 *
 * @return void
 */
function developer_tools_deactivate()
{
	change_admin_permission('developer_tools', false, -1);
	foreach ((array) developerToolsGetAllModules() as $key => $module) {
		change_admin_permission('developer_tools', $key, -1);
	}
}

/**
 * uninstall
 *
 * @return void
 */
function developer_tools_uninstall()
{
	DeveloperToolsInstaller::getInstance()->uninstall();
	DeveloperToolsCache::getInstance()->clear();
}

/**
 * retrieves the plugin's settings group gid if it exists
 * attempts to cache repeat calls
 *
 * @return int setting group id
 */
function developerToolsGetSettingsgroup()
{
	static $gid;

	// if we have already stored the value
	if (!isset($gid)) {
		global $db;

		// otherwise we will have to query the db
		$query = $db->simple_select('settinggroups', 'gid', "name='developer_tools_settings'");
		$gid = (int) $db->fetch_field($query, 'gid');
	}
	return $gid;
}

/**
 * builds the URL to modify plugin settings if given valid info
 *
 * @param  int group id
 * @return string setting group URL
 */
function developerToolsBuildSettingsURL($gid)
{
	if ($gid) {
		return "index.php?module=config-settings&amp;action=change&amp;gid={$gid}";
	}
}

/**
 * builds a link to modify plugin settings if it exists
 *
 * @return setting group link HTML
 */
function developerToolsBuildSettingsLink()
{
	global $lang;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	$gid = developerToolsGetSettingsgroup();

	// does the group exist?
	if ($gid) {
		// if so build the URL
		$url = developerToolsBuildSettingsURL($gid);

		// did we get a URL?
		if ($url) {
			// if so build the link
			return <<<EOF
<a href="{$url}" title="{$lang->developer_tools_plugin_settings}">{$lang->developer_tools_plugin_settings}</a>
EOF;
		}
	}
	return false;
}

/**
 * check plugin requirements and display warnings as appropriate
 *
 * @param  bool
 * @return string warning text
 */
function developerToolsCheckRequirements($deep = false)
{
	global $lang;

	$adminStatus = is_writable(MYBB_ADMIN_DIR . 'styles/');
	if ($deep !== true &&
		$adminStatus) {
		return;
	}

	$issues = '';
	if (!$adminStatus) {
		$issues .= '<br /><span style="font-family: Courier New; font-weight: bolder; font-size: small; color: black;">' . MYBB_ADMIN_DIR . 'styles/</span>';
	}

	if ($deep) {
		$adminSubStatus = developerToolsIsWritable(MYBB_ADMIN_DIR . 'styles/');

		if ($adminStatus &&
			$adminSubStatus) {
			return;
		}

		if (!$adminSubStatus) {
			$issues .= "<br /><span>{$lang->sprintf($lang->developer_tools_subfolders_unwritable, MYBB_ADMIN_DIR . 'styles/</span>')}";
		}
		return "{$lang->developer_tools_folders_requirement_warning}<br />{$issues}";
	}

	return <<<EOF
<br /><br /><div style="border: 1px solid darkred; color: darkred; background: pink;">{$lang->developer_tools_folders_requirement_warning}{$issues}</div>
EOF;
}

/**
 * recursively check mutability of folders
 *
 * @param  string
 * @return bool
 */
function developerToolsIsWritable($rootFolder)
{
	foreach (new DirectoryIterator($rootFolder) as $folder) {
		if (!$folder->isDir() ||
			$folder->isFile() ||
			$folder->isDot()) {
			continue;
		}

		if (!is_writeable($rootFolder . $folder . '/') ||
			!developerToolsIsWritable($rootFolder . $folder . '/')) {
			return false;
		}
	}
	return true;
}

?>
