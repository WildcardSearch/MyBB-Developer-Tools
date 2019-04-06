<?php
/*
 * Plugin Name: Developer Tools for MyBB 1.8.x
 * Copyright 2018 WildcardSearch
 * http://www.rantcentralforums.com
 *
 * this is the main plugin file
 */

// disallow direct access to this file for security reasons.
if (!defined('IN_MYBB')) {
    die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');
}

define('DEVELOPER_TOOLS_VERSION', '0.2');
define('DEVELOPER_TOOLS_MOD_URL', MYBB_ROOT . 'inc/plugins/developer_tools/modules');

// register custom class autoloader
spl_autoload_register('developerToolsClassAutoLoad');

require_once MYBB_ROOT . 'inc/plugins/developer_tools/functions.php';

// load the install/admin routines only if in ACP.
if (defined('IN_ADMINCP')) {
    require_once MYBB_ROOT . 'inc/plugins/developer_tools/install.php';
}

/**
 * class autoloader
 *
 * @param string the name of the class to load
 * @return void
 */
function developerToolsClassAutoLoad($className) {
	$path = MYBB_ROOT . "inc/plugins/developer_tools/classes/{$className}.php";

	if (file_exists($path)) {
		require_once $path;
	}
}

?>
