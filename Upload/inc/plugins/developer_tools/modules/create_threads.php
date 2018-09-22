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
	developerToolsCreateThreadsGetRandomUser($totalUsers);

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
		$tid = developer_tools_create_threads_my_create_thread($fid, $subject, $td++);

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
			$pid = developer_tools_create_threads_my_create_post($tid, $fid, $subject, $postMessage, $d++);
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
function developer_tools_create_threads_my_create_thread($fid = 2, $subject, $dateline)
{
	global $mybb, $session, $li;

	$user = developerToolsCreateThreadsGetRandomUser();

	// Set up posthandler.
	require_once MYBB_ROOT."inc/datahandlers/post.php";
	$posthandler = new PostDataHandler("insert");
	$posthandler->action = "thread";

	// Set the thread data that came from the input to the $thread array.
	$new_thread = array(
		"fid" => $fid,
		"subject" => $subject,
		"uid" => $user['uid'],
		"username" => $user['username'],
		"message" => $li->paragraphs(rand(1, 10)),
		"ipaddress" => $session->packedip,
		"dateline" => $dateline,
	);

	$posthandler->set_data($new_thread);
	if ($posthandler->validate_thread() == false) {
		return false;
	}
	$thread_info = $posthandler->insert_thread();
	$tid = $thread_info['tid'];

	return $tid;
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
function developer_tools_create_threads_my_create_post($tid, $fid, $subject, $message, $dateline)
{
	global $mybb, $session;

	$user = developerToolsCreateThreadsGetRandomUser();

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
		"message" => $message,
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
function developerToolsCreateThreadsGetRandomUser($totalUsers=null)
{
	global $db;

	static $users, $total;

	$randFunction = 'RAND()';
	if ($db->engine == 'pgsql') {
		$randFunction = 'RANDOM()';
	}

	if (!isset($users)) {
		$total = (int) $totalUsers;
		$query = $db->simple_select('users', 'username,uid', '', array("order_by" => $randFunction, "limit" => $total));
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
