<?php

// Disallow direct access to this file for security reasons
if (!defined('IN_MYBB')) {
	die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');
}

require_once MYBB_ROOT . 'inc/plugins/developer_tools/functions.php';
define('DEVELOPER_TOOLS_URL', 'index.php?module=developer_tools');

/**
 * meta info
 *
 * @return bool
 */
function developer_tools_meta()
{
	global $page, $lang;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	if (developerToolsInactive()) {
		return false;
	}

	$subMenu = array();
	$subMenu['10'] = array(
		'id' => 'home',
		'title' => $lang->developer_tools_admin_home,
		'link' => 'index.php?module=developer_tools-phiddle',
	);

	$c = 20;
	foreach((array) developerToolsGetAllModules() as $key => $module) {
		$subMenu[$c] = array(
			'id' => $key,
			'title' => $module->get('title'),
			'link' => "index.php?module=developer_tools-{$key}",
		);
		$c += 10;
	}
	
	$page->add_menu_item($lang->developer_tools, 'developer_tools', 'index.php?module=developer_tools', 100, $subMenu);

	return true;
}

/**
 * action handler
 *
 * @param  string
 * @return string url
 */
function developer_tools_action_handler($action)
{
	global $page;

	if (developerToolsInactive()) {
		return false;
	}

	$page->active_module = 'developer_tools';
	$page->active_action = $action;

	return 'index.php';
}

/**
 * permissions
 *
 * @return array
 */
function developer_tools_admin_permissions()
{
	global $lang, $plugins;

	if (developerToolsInactive()) {
		return false;
	}

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	$admin_permissions = array(
		"developer_tools" => $lang->developer_tools_admin_permissions_desc,
	);

	foreach ((array) developerToolsGetAllModules() as $key => $module) {
		$admin_permissions[$key] = $lang->sprintf($lang->developer_tools_page_permissions_desc, $module->get('title'));
	}

	$admin_permissions = $plugins->run_hooks("admin_developer_tools_permissions", $admin_permissions);

	return array("name" => $lang->developer_tools, "permissions" => $admin_permissions, "disporder" => 100);
}

/**
 * deternmine if the plugin is inactive
 *
 * @return bool
 */
function developerToolsInactive()
{
	global $cache;

	$plugin_list = $cache->read('plugins');
	return (empty($plugin_list['active']) ||
		!is_array($plugin_list['active']) ||
		!in_array('developer_tools', $plugin_list['active']));
}

?>
