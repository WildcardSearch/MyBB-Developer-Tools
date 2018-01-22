<?php
/*
 * Plugin Name: Picture Perfect for MyBB 1.8.x
 * Copyright 2018 WildcardSearch
 * http://www.rantcentralforums.com
 *
 * this file contains functions used by the PHiddle
 */

function developerToolsWriteTemp($userCode)
{
	$code = <<<EOF
<?php

define('IN_MYBB', 1);
define('NO_ONLINE', 1);
require_once '../../../../global.php';

{$userCode}

?>

EOF;
	file_put_contents(DEVELOPER_TOOLS_SANDBOX_FILE_PATH, $code);
}

function developerToolsNewProject()
{
	global $mybb, $html;

	$myCache = DeveloperToolsCache::getInstance();
	$codeArray = $myCache->read('php_code');

	$codeArray[$mybb->user['uid']] = '';
	$myCache->update('php_code', $codeArray);

	flash_message('Project code cleared.', 'success');
	admin_redirect($html->url());
}

?>
