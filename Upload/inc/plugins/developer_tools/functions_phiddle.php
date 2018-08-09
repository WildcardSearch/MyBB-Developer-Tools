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
	global $mybb, $html, $myCache;

	$codeArray = $myCache->read('php_code');

	$codeArray[$mybb->user['uid']] = '';
	$myCache->update('php_code', $codeArray);
	my_setcookie('phiddle_project', 0);

	flash_message('Project code cleared.', 'success');
	admin_redirect($html->url());
}

function developerToolsLoadProject()
{
	global $mybb, $lang, $db, $html, $page;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	$query = $db->simple_select('phiddles', 'id,title');
	$count = $db->num_rows($query);
	if ($count == 0) {
		flash_message('There are no saved Phiddles to load.', 'error');
		admin_redirect($html->url());
	}

	while ($phiddle = $db->fetch_array($query)) {
		$options[$phiddle['id']] = $phiddle['title'];
	}

	$size = 10;
	if ($count < 10) {
		$size = $count;
	}

	$page->extra_header .= <<<EOF
<style>
select.phiddleList {
	margin: 10px;
	font-size: 1.2em;
	font-weight: bold;
}
</style>

EOF;

	$page->add_breadcrumb_item('Load a PHiddle');
	$page->output_header("{$lang->developer_tools} &mdash; Load");

	$form = new Form($html->url(), 'post');
	$formContainer = new FormContainer('Open a Phiddle');

	$formContainer->output_row('Select a Phiddle to load', 'select a project from the list', $form->generate_select_box('phiddle', $options, '', array('id' => 'phiddle_select', 'size' => $size, 'class' => 'phiddleList')), 'phiddle');

	$formContainer->end();

	$buttons[] = $form->generate_submit_button('Load', array('name' => 'load_phiddle'));
	$buttons[] = $form->generate_submit_button('Cancel', array('name' => 'cancel_load'));
	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
	exit;
}

function developerToolsSaveProject()
{
	global $mybb, $db, $phiddle, $myCache, $html;

	$phiddle->set('content', $mybb->input['php_code']);
	$phiddle->save();

	$codeArray = $myCache->read('php_code');

	$codeArray[$mybb->user['uid']] = $mybb->input['php_code'];
	$myCache->update('php_code', $codeArray);

	flash_message('Phiddle saved successfully', 'success');
	admin_redirect($html->url());
}

function developerToolsSaveProjectAs()
{
	global $mybb, $lang, $db, $html, $page, $phiddle, $myCache;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	$codeArray = $myCache->read('php_code');
	$codeArray[$mybb->user['uid']] = $mybb->input['php_code'];
	$myCache->update('php_code', $codeArray);

	$page->add_breadcrumb_item('Save PHiddle As...');
	$page->output_header("{$lang->developer_tools} &mdash; Save As...");

	$form = new Form($html->url(), 'post');
	$formContainer = new FormContainer('Save PHiddle As...');

	$formContainer->output_row('Title', 'enter a title for your PHiddle here', $form->generate_text_box('title', ''));

	$formContainer->end();

	$buttons[] = $form->generate_submit_button('Save', array('name' => 'save_phiddle'));
	$buttons[] = $form->generate_submit_button('Cancel', array('name' => 'cancel_save'));
	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
	exit;
}

?>
