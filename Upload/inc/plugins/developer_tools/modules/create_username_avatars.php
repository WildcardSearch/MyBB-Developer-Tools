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
function developer_tools_create_username_avatars_info()
{
	global $lang, $cp_style;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	return array(
		'title' => $lang->developer_tools_create_username_avatars_title,
		'description' => $lang->developer_tools_create_username_avatars_description,
		'version' => '1.1',
		'longDescription' => <<<EOF

			<p style="text-align: left;">{$lang->developer_tools_create_username_avatars_long_description_1}</p>
			<p>{$lang->developer_tools_create_username_avatars_long_description_2}</p>
			<p><img src="https://i.imgur.com/PiwJTLe.png" alt="{$lang->developer_tools_create_username_avatars_long_description_img_alt}" style="width: 50px;" title="{$lang->developer_tools_create_username_avatars_long_description_img_title}" /></p>
EOF
		,
		'settings' => array(
			'background' => array(
				'title' => $lang->developer_tools_create_username_avatars_background_title,
				'description' => $lang->developer_tools_create_username_avatars_background_desc,
				'optionscode' => <<<EOF
select
solid=Solid
gradient=Gradient
EOF
				,
				'value' => 'gradient',
			),
			'background_color' => array(
				'title' => $lang->developer_tools_create_username_avatars_background_color_title,
				'description' => $lang->developer_tools_create_username_avatars_background_color_desc,
				'optionscode' => <<<EOF
select
white=White
lightgray=Light Gray
darkgray=Dark Gray
red=Red
green=Green
blue=Blue
yellow=Yellow
purple=Purple
EOF
				,
				'value' => 'blue',
			),
			'display' => array(
				'title' => $lang->developer_tools_create_username_avatars_display_title,
				'description' => $lang->developer_tools_create_username_avatars_display_desc,
				'optionscode' => <<<EOF
select
initials=Initials
fullname=Full Name
EOF
				,
				'value' => 'initials',
			),
			'color' => array(
				'title' => $lang->developer_tools_create_username_avatars_color_title,
				'description' => $lang->developer_tools_create_username_avatars_color_desc,
				'optionscode' => 'text',
				'value' => '#ffffff',
			),
			'shadow' => array(
				'title' => $lang->developer_tools_create_username_avatars_shadow_title,
				'description' => $lang->developer_tools_create_username_avatars_shadow_desc,
				'optionscode' => 'text',
				'value' => '#808080',
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
function developer_tools_create_username_avatars_execute($settings)
{
	global $mybb, $db, $html, $li, $lang;

	require_once MYBB_ROOT.'inc/functions_upload.php';

	$font = MYBB_ROOT.'inc/plugins/developer_tools/data/fonts/arialbd.ttf';
	$count = 0;
	$users = array();
	$query = $db->simple_select('users', '*', '', array('orderby' => 'uid', 'orderdir' => 'ASC'));

	if ($db->num_rows($query) == 0) {
		flash_message($lang->developer_tools_create_username_avatars_error_message_no_users, 'error');
		admin_redirect($html->url());
	}

	$c = dtHexToRgb($settings['color']);
	$d = dtHexToRgb($settings['shadow']);

	while ($user = $db->fetch_array($query)) {
		$uid = (int) $user['uid'];

		$im = dtGetBackgroundImage($settings);

		$textColor = imagecolorallocate($im, $c[0], $c[1], $c[2]);
		$shadow = imagecolorallocate($im, $d[0], $d[1], $d[2]);

		$namePieces = explode(' ', $user['username']);
		$namePieceCount = count($namePieces);

		if ($settings['display'] == 'fullname') {
			$startingFontSize = (int) 100 / $namePieceCount;
			$fontSize = dtGetFontSize($font, $namePieces, $startingFontSize);

			$y = 45 - ($fontSize / 2) * ($namePieceCount - 2);
			for ($i = 0; $i < $namePieceCount; $i++) {
				$d = imagettfbbox($fontSize, 0, $font, $namePieces[$i]);

				$width = abs($d[4] - $d[0]);
				$height = abs($d[5] - $d[1]);

				$x = (int) 50 - ($width / 2);
				imagettftext($im, $fontSize, 0, $x, $y, $shadow, $font, $namePieces[$i]);
				imagettftext($im, $fontSize, 0, $x-1, $y-1, $textColor, $font, $namePieces[$i]);
				$y = (int) $y + ($fontSize * 1.2);
			}
		} else {
			$flString = '';
			foreach ($namePieces as $piece) {
				$flString .= my_strtoupper(substr($piece, 0, 1));
			}

			$fontSize = dtGetFontSize($font, $flString, 50);

			$d = imagettfbbox($fontSize, 0, $font, $flString);

			$width = abs($d[4] - $d[0]);
			$height = abs($d[5] - $d[1]);

			$x = (int) 50 - ($width / 2);
			$y = (int) 50 + ($height / 2);
			imagettftext($im, $fontSize, 0, $x, $y, $grey, $font, $flString);
			imagettftext($im, $fontSize, 0, $x-3, $y-3, $textColor, $font, $flString);
		}

		$path = MYBB_ROOT.'uploads/avatars';
		if (!file_exists($path) &&
			!@mkdir($path)) {
			flash_message($lang->developer_tools_create_username_avatars_error_message_folder, 'error');
			admin_redirect($html->url());
		}
		$filename = "{$path}/avatar_{$uid}.png";

		// write the image file
		imagepng($im, $filename);
		imagedestroy($im);

		$updatedAvatar = array(
			'avatar' => $db->escape_string("./uploads/avatars/avatar_{$uid}.png?dateline=".TIME_NOW),
			'avatardimensions' => '100|100',
			'avatartype' => 'remote',
		);

		$db->update_query('users', $updatedAvatar, "uid='{$uid}'");
		$count++;
	}

	flash_message($lang->sprintf($lang->developer_tools_create_username_avatars_success_message, $count), 'success');
	admin_redirect($html->url());
}

/**
 * get a background color for the avatar or a gradient array
 *
 * @param  array
 * @param  string solid|gradient
 * @return string|array
 */
function dtGetBackgroundColor($settings, $key='gradient')
{
	static $colors = array(
		'white' => array(
			'hex' => '#ffffff',
			'gradient' => array(
				'#ffffff','#bbbbbb','#aaaaaa','#cccccc',
			),
		),
		'lightgray' => array(
			'hex' => '#d3d3d3',
			'gradient' => array(
				'#d3d3d3','#939393','#838383','#a3a3a3',
			),
		),
		'darkgray' => array(
			'hex' => '#a9a9a9',
			'gradient' => array(
				'#a9a9a9','#696969','#595959','#797979',
			),
		),
		'red' => array(
			'hex' => '#ff3333',
			'gradient' => array(
				'#ff3333','#aa3333','#993333','#bb3333',
			),
		),
		'green' => array(
			'hex' => '#33ff33',
			'gradient' => array(
				'#33ff33','#33aa33','#339933','#33bb33',
			),
		),
		'blue' => array(
			'hex' => '#3333ff',
			'gradient' => array(
				'#3333ff','#3333aa','#333399','#3333bb',
			),
		),
		'yellow' => array(
			'hex' => '#ffff33',
			'gradient' => array(
				'#ffff33','#bbbb33','#aaaa33','#bbbb33',
			),
		),
		'purple' => array(
			'hex' => '#dd33ff',
			'gradient' => array(
				'#dd33ff','#7733bb','#6633aa','#8833bb',
			),
		),
	);

	return $colors[$settings['background_color']][$key];
}

/**
 * get a background image per settings
 *
 * @param  array
 * @return resource
 */
function dtGetBackgroundImage($settings)
{
	static $bgImage = null;

	extract($settings);

	if ($background == 'solid') {
		$im = @imagecreatetruecolor(100, 100);

		$thisColor = dtGetBackgroundColor($settings, 'hex');
		$c = dtHexToRgb($thisColor);
		$bgColor = imagecolorallocate($im, $c[0], $c[1], $c[2]);

		imagefilledrectangle($im, 0, 0, 99, 99, $bgColor);
	} else {
		$thisColor = dtGetBackgroundColor($settings);

		if ($bgImage == null) {
			$bgImage = dtGradientImage(100, 100, $thisColor);
		}

		$im = dtCloneImage($bgImage);
	}

	return $im;
}

/**
 * get an appropriate font size for the length of the text
 *
 * @param  string path
 * @param  string|array text
 * @param  int starting font size
 * @return int
 */
function dtGetFontSize($font, $pieces, $start)
{
	if (!is_array($pieces)) {
		$pieces = (array) $pieces;
	}

	$pieceCount = count($pieces);
	$maxHeight = (int) 90 / $pieceCount;

	$string = array_shift($pieces);
	foreach ($pieces as $piece) {
		if (my_strlen($piece) > my_strlen($string)) {
			$string = $piece;
		}
	}

	if (!$string) {
		return $start;
	}

	$fontSize = $start;

	do {
		$fontSize--;
		$d = imagettfbbox($fontSize, 0, $font, $string);
		$width = abs($d[4] - $d[0]);
		$height = abs($d[5] - $d[1]);
	} while ($width > 85 || $height > $maxHeight);

	return $fontSize;
}

/**
 * clone an image resource
 *
 * @param  resource
 * @return resource
 */
function dtCloneImage($image)
{
	$copy = imagecreatetruecolor(100, 100);

    imagecopy($copy, $image, 0, 0, 0, 0, 100, 100);

    return $copy;
}

/**
 * Generates a gradient image
 *
 * @author Christopher Kramer
 *
 * @param  int width in px
 * @param  int height in px
 * @param  array color array with 4 elements:
 *			0: top left color, 1: top right color,
 * 			2: bottom left color, 3: bottom right color
 * @param  bool true (default) to accept hex-strings, false to use an rgb array
 * @return resource
 */
function dtGradientImage($w=100, $h=100, $c=array('#FFFFFF','#FF0000','#00FF00','#0000FF'), $hex=true)
{
    $im = imagecreatetruecolor($w, $h);

    if ($hex) {
        for ($i=0; $i<=3; $i++) {
            $c[$i] = dtHexToRgb($c[$i]);
        }
    }

    $rgb=$c[0];
    for ($x = 0; $x <= $w; $x++) {
        for ($y = 0; $y <= $h; $y++) {
            $col = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
            imagesetpixel($im, $x-1, $y-1, $col);

            for ($i = 0; $i <= 2; $i++) {
                $rgb[$i] =
                    $c[0][$i]*(($w-$x)*($h-$y)/($w*$h)) +
                    $c[1][$i]*($x     *($h-$y)/($w*$h)) +
                    $c[2][$i]*(($w-$x)*$y     /($w*$h)) +
                    $c[3][$i]*($x     *$y     /($w*$h));
            }
        }
    }

    return $im;
}

/**
 * converts a hex color to rgb
 *
 * @author Christopher Kramer
 *
 * @param  string hex color number
 * @return array
 */
function dtHexToRgb($hex)
{
    $rgb[0] = hexdec(substr($hex, 1, 2));
    $rgb[1] = hexdec(substr($hex, 3, 2));
    $rgb[2] = hexdec(substr($hex, 5, 2));

    return $rgb;
}

?>
