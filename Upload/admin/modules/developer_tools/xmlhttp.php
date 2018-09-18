<?php

define('IN_MYBB', true);
require_once '../../../global.php';
require_once MYBB_ROOT.'inc/plugins/developer_tools/functions_phiddle.php';

$myCache = DeveloperToolsCache::getInstance();

if ($mybb->request_method != 'post' ||
	$mybb->input['mode'] != 'phiddle') {
	exit;
}

switch ($mybb->input['action']) {
case 'new':
	developerToolsNewProject();
	break;
}

?>
