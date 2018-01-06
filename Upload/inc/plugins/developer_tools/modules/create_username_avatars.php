<?php
/*
 * Plugin Name: Picture Perfect for MyBB 1.8.x
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
function developer_tools_create_username_avatars_info()
{
	global $lang, $cp_style;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	return array(
		'title' => $lang->developer_tools_create_username_avatars_title,
		'description' => $lang->developer_tools_create_username_avatars_description,
		'version' => '1.0',
		'longDescription' => <<<EOF

			<p style="text-align: left;">{$lang->developer_tools_create_username_avatars_long_description_1}</p>
			<p>{$lang->developer_tools_create_username_avatars_long_description_2}</p>
			<p><img src="https://i.imgur.com/GcjCuDi.png" alt="{$lang->developer_tools_create_username_avatars_long_description_img_alt}" style="width: 50px;" title="{$lang->developer_tools_create_username_avatars_long_description_img_title}" /></p>
EOF
		,
	);
}

/**
 * execute ACP page
 *
 * @param  array
 * @return void
 */
function developer_tools_create_username_avatars_execute($settings)
{
	global $mybb, $db, $html, $li, $lang;

	require_once MYBB_ROOT . 'inc/functions_upload.php';

	$font = MYBB_ROOT . 'inc/plugins/developer_tools/data/fonts/arialbd.ttf';
	$count = 0;
	$users = array();
	$query = $db->simple_select('users', '*', 'NOT uid=1', array('orderby' => 'uid', 'orderdir' => 'ASC'));

	if ($db->num_rows($query) == 0) {
		flash_message($lang->developer_tools_create_username_avatars_error_message_no_users, 'error');
		admin_redirect($html->url());
	}

	while ($user = $db->fetch_array($query)) {
		$uid = (int) $user['uid'];
		$im = @imagecreatetruecolor(100, 100);

		$white = imagecolorallocate($im, 255, 255, 255);
		$grey = imagecolorallocate($im, 128, 128, 128);
		$black = imagecolorallocate($im, 0, 0, 0);

		imagefilledrectangle($im, 0, 0, 99, 99, $white);

		$namePieces = explode(' ', $user['username']);
		$namePieceCount = count($namePieces);

		/*
		 * the font is 18px tall, so if we give 2px padding
		 * that leaves a line height of 20px
		 * for every piece that we add (space-separated names)
		 * we need to start 10px sooner (half line-height)
		 * and obviously draw more names
		 */
		$y = 45 - (10 * ($namePieceCount - 1));
		for ($x = 0; $x < $namePieceCount; $x++) {
			imagettftext($im, 18, 0, 0, $y, $grey, $font, $namePieces[$x]);
			imagettftext($im, 18, 0, 1, $y + 1, $black, $font, $namePieces[$x]);
			$y = $y + 20;
		}

		$path = MYBB_ROOT . 'uploads/avatars';
		if (!file_exists($path) &&
			!@mkdir($path)) {
			flash_message($lang->developer_tools_create_username_avatars_error_message_folder, 'error');
			admin_redirect($html->url());
		}
		$filename = "{$path}/avatar_{$uid}.png";
		imagepng($im, $filename);
		imagedestroy($im);

		$updatedAvatar = array(
			'avatar' => $db->escape_string('./' . $filename . '?dateline=' . TIME_NOW),
			'avatardimensions' => '100|100',
			'avatartype' => 'remote',
		);

		$db->update_query('users', $updatedAvatar, "uid='{$uid}'");
		$count++;
	}

	flash_message($lang->sprintf($lang->developer_tools_create_username_avatars_success_message, $count), 'success');
	admin_redirect($html->url());
}

?>
