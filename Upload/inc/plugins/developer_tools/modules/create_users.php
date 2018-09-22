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
function developer_tools_create_users_info()
{
	global $lang;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	return array(
		'title' => $lang->developer_tools_create_users_title,
		'description' => $lang->developer_tools_create_users_description,
		'version' => '1.0',
		'settings' => array(
			'amount' => array(
				'title' => $lang->developer_tools_create_users_amount_title,
				'description' => $lang->developer_tools_create_users_amount_desc,
				'optionscode' => 'numeric',
				'value' => '2',
			),
			'usergroup' => array(
				'title' => $lang->developer_tools_create_users_usergroup_title,
				'description' => $lang->developer_tools_create_users_usergroup_desc,
				'optionscode' => 'groupselectsingle',
				'value' => '2',
			),
			'password' => array(
				'title' => $lang->developer_tools_create_users_password_title,
				'description' => $lang->developer_tools_create_users_password_desc,
				'optionscode' => 'passwordbox',
				'value' => 'd0l@n415',
			),
			'email' => array(
				'title' => $lang->developer_tools_create_users_email_title,
				'description' => $lang->developer_tools_create_users_email_desc,
				'optionscode' => 'text',
				'value' => '',
			),
			'name_count' => array(
				'title' => $lang->developer_tools_create_users_name_count_title,
				'description' => $lang->developer_tools_create_users_name_count_desc,
				'optionscode' => <<<EOF
numeric
min=1
max=3
EOF
				,
				'value' => '2',
			),
			'caps' => array(
				'title' => $lang->developer_tools_create_users_caps_title,
				'description' => $lang->developer_tools_create_users_caps_desc,
				'optionscode' => 'yesno',
				'value' => '1',
			),
			'local_names' => array(
				'title' => $lang->developer_tools_create_users_local_names_title,
				'description' => $lang->developer_tools_create_users_local_names_desc,
				'optionscode' => 'yesno',
				'value' => '0',
			),
			'referrer' => array(
				'title' => $lang->developer_tools_create_users_referrer_title,
				'description' => $lang->developer_tools_create_users_referrer_desc,
				'optionscode' => 'text',
				'value' => '',
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
function developer_tools_create_users_execute($settings)
{
	global $html, $mybb, $lang, $firstNames, $lastNames;

	extract($settings);
	if ($amount == 0) {
		$amount = 10;
	}

	// Set up user handler.
	require_once MYBB_ROOT . 'inc/datahandlers/user.php';

	$mybb->settings['allowmultipleemails'] = 1;

	$nameSource = 'international';
	if ($local_names) {
		$nameSource = 'local';
	}

	require_once MYBB_ROOT . "inc/plugins/developer_tools/data/names/{$nameSource}/names.php";

	if (!$password ||
		my_strlen($password) == 0) {
		$password = uniqid();
	}

	if (!$email ||
		my_strlen($email) == 0) {
		$email = 'admin@localhost.com';
	}

	// Set the data for the new user.
	$user = array(
		'password' => $password,
		'password2' => $password,
		'email' => $email,
		'email2' => $email,
		'usergroup' => $usergroup,
		'regip' => '127.0.0.1',
		'longregip' => my_ip2long('127.0.0.1'),
		'referrer' => $referrer,
	);

	$addedNames = '';
	$addedNameCount = 0;
	while ($addedNameCount < $amount) {
		$userhandler = new UserDataHandler('insert');
		$user['username'] = developerToolsCreateUsersAssembleName($name_count, $caps);
		$userhandler->set_data($user);
		if (!$userhandler->validate_user()) {
			continue;
		}

		$userhandler->insert_user();
		$addedNameCount++;
	}

	flash_message($lang->sprintf($lang->developer_tools_create_users_success_message, $addedNameCount), 'success');
	admin_redirect($html->url());
}

/**
 * build a user name per parameters
 *
 * @param  int
 * @param  bool
 * @return string
 */
function developerToolsCreateUsersAssembleName($maxNames, $caps)
{
	global $mybb, $firstNames, $lastNames;

	$names = array();
	if ($maxNames <= 1) {
		$names[] = developerToolsCreateUsersGetName($firstNames, $caps);
	} else {
		while (count($names) < ($maxNames - 1)) {
			$names[] = developerToolsCreateUsersGetName($firstNames, $caps, $leet, $special);
		}
		$names[] = developerToolsCreateUsersGetName($lastNames, $caps);
	}

	while (count($names) > 1 &&
		strlen(implode(' ', $names)) > $mybb->settings['maxnamelength']) {
		array_shift($names);
	}
	return implode(' ', $names);
}

/**
 * get a random name from the list and capitalize if required
 *
 * @param  array
 * @param  bool
 * @return string
 */
function developerToolsCreateUsersGetName($names, $caps = false)
{
	$name = mb_strtolower($names[rand(0, count($names) - 1)]);

	if ($caps) {
		$name = ucwords($name);
	}
	return $name;
}

?>
