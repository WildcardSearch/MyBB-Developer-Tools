<?php
/*
 * Plugin Name: Developer Tools for MyBB 1.8.x
 * Copyright 2018 WildcardSearch
 * http://www.rantcentralforums.com
 *
 * default module
 */

/**
 * module info
 *
 * @return void
 */
function developer_tools_create_threads_info()
{
	global $lang;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	return array(
		'title' => $lang->developer_tools_create_threads_title,
		'description' => $lang->developer_tools_create_threads_description,
		'version' => '1.0',
		'settings' => array(
			'threadcount' => array(
				'title' => $lang->developer_tools_create_threads_threadcount_title,
				'description' => $lang->developer_tools_create_threads_threadcount_desc,
				'optionscode' => <<<EOF
numeric
min=1
max=100
EOF
				,
				'value' => '1',
			),
			'postcount' => array(
				'title' => $lang->developer_tools_create_threads_postcount_title,
				'description' => $lang->developer_tools_create_threads_postcount_desc,
				'optionscode' => <<<EOF
numeric
min=0
max=1000
step=10
EOF
				,
				'value' => '10',
			),
			'fid' => array(
				'title' => $lang->developer_tools_create_threads_fid_title,
				'description' => $lang->developer_tools_create_threads_fid_desc,
				'optionscode' => 'forumselectsingle',
				'value' => '2',
			),
			'image_folder' => array(
				'title' => $lang->developer_tools_create_threads_image_folder_title,
				'description' => $lang->developer_tools_create_threads_image_folder_desc,
				'optionscode' => 'text',
				'value' => '',
			),
			'use_banned_members' => array(
				'title' => $lang->developer_tools_create_threads_use_banned_members_title,
				'description' => $lang->developer_tools_create_threads_use_banned_members_desc,
				'optionscode' => 'yesno',
				'value' => '0',
			),
		),
	);
}

/**
 * execute ACP page
 *
 * @param  array
 * @return void
 */
function developer_tools_create_threads_execute($settings)
{
	global $mybb, $db, $html, $li, $lang;

	require_once MYBB_ROOT.'inc/plugins/developer_tools/joshtronic/LoremIpsum.php';
	$li = new joshtronic\LoremIpsum();

	$threadCount = (int) $settings['threadcount'];
	$postCount = (int) ($settings['postcount'] + 1);
	$totalUsers = (int) $threadCount + $postCount;
	developerToolsCreateThreadsGetRandomUser($settings['use_banned_members'], $totalUsers);

	$fid = (int) $settings['fid'];

	$query = $db->simple_select('forums', 'type', "fid={$fid}");
	if ($db->num_rows($query) == 0) {
		flash_message($lang->developer_tools_create_threads_error_message_no_forum, 'error');
		admin_redirect($html->url());
	}

	$forumType = $db->fetch_field($query, 'type');
	if ($forumType != 'f') {
		flash_message($lang->developer_tools_create_threads_error_message_category, 'error');
		admin_redirect($html->url());
	}

	$td = TIME_NOW - $totalUsers;
	for ($t = 1; $t <= $threadCount; $t++) {
		$subject = ucwords($li->words(rand(1, 3)));
		$tid = developer_tools_create_threads_my_create_thread($fid, $subject, $td++, $settings);

		if ($tid == false) {
			$t--;
			continue;
		}

		$tids[] = $tid;
		$d = $td + 1;
		for ($p = 1; $p < $postCount; $p++) {
			$postMessage = $li->paragraphs(rand(1, 3));
			if (!$postMessage) {
				$postMessage = 'Generic Message #'.$p;
			}
			$pid = developer_tools_create_threads_my_create_post($tid, $fid, $subject, $postMessage, $d++, $settings);
			if ($pid == false) {
				$p--;
				continue;
			}
		}

		require_once MYBB_ROOT."inc/functions_indicators.php";
		mark_thread_read($tid, $fid);
	}

	$postTotal = $threadCount * ($postCount - 1);

	flash_message($lang->sprintf($lang->developer_tools_create_threads_success_message, $threadCount, $postTotal), 'success');
	admin_redirect($html->url());
}

/**
 * create a single thread per parameters
 *
 * @param  int
 * @param  string
 * @param  int
 * @return int tid
 */
function developer_tools_create_threads_my_create_thread($fid = 2, $subject, $dateline, $settings)
{
	global $mybb, $session, $li;

	$user = developerToolsCreateThreadsGetRandomUser($settings['use_banned_members']);

	// Set up posthandler.
	require_once MYBB_ROOT."inc/datahandlers/post.php";
	$posthandler = new PostDataHandler("insert");
	$posthandler->action = "thread";

	// Set the thread data that came from the input to the $thread array.
	$newThread = array(
		"fid" => $fid,
		"subject" => $subject,
		"uid" => $user['uid'],
		"username" => $user['username'],
		"message" => developerToolsGetMessage($settings),
		"ipaddress" => $session->packedip,
		"dateline" => $dateline,
	);

	$posthandler->set_data($newThread);
	if ($posthandler->validate_thread() == false) {
		return false;
	}
	$threadInfo = $posthandler->insert_thread();
	$tid = $threadInfo['tid'];

	return $tid;
}

/**
 * get a random lorem ispsum message and image if applicable
 *
 * @param  array module settings
 * @return string message
 */
function developerToolsGetMessage($settings)
{
	global $mybb, $li;

	// clean up the image folder
	$imageFolder = trim($settings['image_folder']);

	if (substr($imageFolder, strlen($imageFolder) - 1) == '/') {
		$imageFolder = substr($imageFolder, 0, strlen($imageFolder) - 1);
	}

	if (substr($imageFolder, 0, 1) == '/') {
		$imageFolder = substr($imageFolder, 1);
	}

	// add a random image if available
	$message = '';
	if ($imageFolder &&
		file_exists(MYBB_ROOT.$imageFolder)) {
		$imageFile = developerToolsGetImage(MYBB_ROOT.$imageFolder);
		if ($imageFile) {
			$image = $mybb->settings['bburl']."/{$imageFolder}/".$imageFile;
		}

		if ($image) {
			$message = "[img]{$image}[/img]\n\n";
		}
	}

	// add some random paragraphs
	$message .= $li->paragraphs(rand(1, 10));

	return $message;
}

/**
 * get a random lorem ispsum message and image if applicable
 *
 * @param  string relative folder path
 * @return string file path
 */
function developerToolsGetImage($imageFolder)
{
	static $imageList = null;

	// only build the image cache once
	if (!isset($imageList)) {
		$imageList = array();
		foreach (new DirectoryIterator($imageFolder) as $file) {
			if (!$file->isFile() ||
				$file->isDot() ||
				$file->isDir()) {
				continue;
			}

			$extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);

			// only PHP files
			if (!in_array($extension, array('gif', 'png', 'jpg', 'jpeg', 'bmp'))) {
				continue;
			}

			// attempt to load the module
			$imageList[] = $file->getFilename();
		}
	}

	return $imageList[rand(0, count($imageList)-1)];
}

/**
 * create a single post per parameters
 *
 * @param  int
 * @param  int
 * @param  string
 * @param  string
 * @param  int
 * @return int pid
 */
function developer_tools_create_threads_my_create_post($tid, $fid, $subject, $message, $dateline, $settings)
{
	global $mybb, $session;

	$user = developerToolsCreateThreadsGetRandomUser($settings['use_banned_members']);

	// Set up posthandler.
	require_once MYBB_ROOT."inc/datahandlers/post.php";
	$posthandler = new PostDataHandler("insert");

	// Set the post data that came from the input to the $post array.
	$post = array(
		"tid" => $tid,
		"fid" => $fid,
		"subject" => $subject,
		"uid" => $user['uid'],
		"username" => $user['username'],
		"message" => developerToolsGetMessage($settings),
		"ipaddress" => $session->packedip,
		"dateline" => $dateline,
	);

	$posthandler->set_data($post);
	if ($posthandler->validate_post() == false) {
		//die(var_dump($post));
		return false;
	}
	$postinfo = $posthandler->insert_post();

	//die(var_dump($post_info));
	return $postinfo['pid'];
}

/**
 * retrieve a random forum user
 *
 * @param  int|null
 * @return int pid
 */
function developerToolsCreateThreadsGetRandomUser($useBannedMembers=false, $totalUsers=null)
{
	global $db;

	static $users, $total;

	if (!isset($users)) {
		$randFunction = 'RAND()';
		if ($db->engine == 'pgsql') {
			$randFunction = 'RANDOM()';
		}

		$where = '';
		if (!$useBannedMembers) {
			$bannedGroups = array();
			$query = $db->simple_select('usergroups', '*', 'isbannedgroup=1');
			while ($gid = $db->fetch_field($query, 'gid')) {
				$bannedGroups[] = $gid;
			}

			if (is_array($bannedGroups) &&
				!empty($bannedGroups)) {
				if (count($bannedGroups) > 1) {
					$bannedList = implode(',', $bannedGroups);
					$where = "usergroup NOT IN({$bannedList})";
				} elseif (count($bannedGroups) == 1) {
					$where = "usergroup !='{$bannedGroups[0]}'";
				}
			}
		}

		$total = (int) $totalUsers;
		$query = $db->simple_select('users', 'username,uid', $where, array('order_by' => $randFunction, 'limit' => $total));
		while ($user = $db->fetch_array($query)) {
			$users[] = $user;
		}
	}

	if ($totalUsers !== null) {
		return;
	}

	return $users[(int) rand(0, $total - 1)];
}

?>
