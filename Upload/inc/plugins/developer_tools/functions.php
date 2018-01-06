<?php
/*
 * Plugin Name: Picture Perfect for MyBB 1.8.x
 * Copyright 2018 WildcardSearch
 * http://www.rantcentralforums.com
 *
 * functions file
 */

// disallow direct access to this file for security reasons.
if (!defined('IN_MYBB')) {
    die('Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.');
}

/**
 * retrieve any detected modules
 *
 * @return array PicturePerfectModule
 */
function developerToolsGetAllModules()
{
	static $returnArray = array();

	if (!empty($returnArray)) {
		return $returnArray;
	}

	// load all detected modules
	foreach (new DirectoryIterator(DEVELOPER_TOOLS_MOD_URL) as $file) {
		if (!$file->isFile() ||
			$file->isDot() ||
			$file->isDir()) {
			continue;
		}

		$extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);

		// only PHP files
		if ($extension != 'php') {
			continue;
		}

		// extract the baseName from the module file name
		$filename = $file->getFilename();
		$module = substr($filename, 0, strlen($filename) - 4);

		// attempt to load the module
		$returnArray[$module] = new DeveloperToolsAcpModule($module);
	}
	return $returnArray;
}

?>
