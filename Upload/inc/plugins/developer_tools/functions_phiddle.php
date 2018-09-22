<?php
/*
 * Plugin Name: Developer Tools for MyBB 1.8.x
 * Copyright 2018 WildcardSearch
 * http://www.rantcentralforums.com
 *
 * this file contains functions used by the PHiddle
 */

/**
 * write temporary script
 *
 * @param  string
 * @return void
 */
function developerToolsWriteTemp($userCode)
{
	global $mybb, $lang;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	$code = <<<EOF
<?php

define('IN_MYBB', 1);
define('NO_ONLINE', 1);
require_once '../../../../../global.php';

ini_set('display_errors', '1');

{$userCode}

?>

EOF;

	$folder = MYBB_ADMIN_DIR."modules/developer_tools/sandbox/{$mybb->user['uid']}";
	if (!file_exists($folder) &&
		@!mkdir($folder, 0775)) {
		flash_message($lang->developer_tools_error_sandbox_folder, 'error');
		admin_redirect($html->url());
	}

	file_put_contents($folder.'/index.php', $code);
}

/**
 * clear project data
 *
 * @param  bool
 * @return void
 */
function developerToolsNewProject($keepCode=false)
{
	global $mybb, $myCache;

	my_unsetcookie("phiddle_project{$mybb->user['uid']}");

	if ($keepCode) {
		return;
	}

	$codeArray = $myCache->read('php_code');
	$codeArray[$mybb->user['uid']] = '';
	$myCache->update('php_code', $codeArray);
}

/**
 * show load page/modal
 *
 * @param  bool
 * @return void
 */
function developerToolsLoadProject($ajax=false)
{
	global $page, $lang, $html;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	$selectHtml = developerToolsCreatePhiddleSelect();
	if (!$selectHtml) {
		if ($ajax) {
			exit;
		}

		flash_message($lang->developer_tools_error_load_no_phiddles, 'error');
		admin_redirect($html->url());
	}

	$css = <<<EOF

<style>
select.phiddleList {
	margin: 10px;
	font-size: 1.2em;
}
</style>
EOF;

	if ($ajax) {
		echo <<<EOF
<div class="modal" style="width: 540px;">{$css}

EOF;
	} else {
		$page->extra_header .= $css;
		$page->add_breadcrumb_item($lang->developer_tools_breadcrumb_load);
		$page->output_header("{$lang->developer_tools} &mdash; {$lang->developer_tools_breadcrumb_load}");
	}

	$form = new Form($html->url(array('action' => 'doLoad')), 'post', 'modal_form');
	$formContainer = new FormContainer($lang->developer_tools_breadcrumb_load);

	$formContainer->output_row($lang->developer_tools_phiddle_select_title, $lang->developer_tools_phiddle_select_description, $selectHtml, 'phiddle');

	$formContainer->end();

	$buttons[] = $form->generate_submit_button($lang->developer_tools_load_button_title, array('name' => 'load_phiddle', 'id' => 'modalSubmit'));
	$buttons[] = $form->generate_submit_button($lang->developer_tools_cancel_button_title, array('name' => 'cancel_load', 'id' => 'modalCancel'));
	$form->output_submit_wrapper($buttons);
	$form->end();

	if ($ajax) {
		echo "\n</div>\n";
	} else {
		$page->output_footer();
	}

	exit;
}

/**
 * load project data
 *
 * @return void
 */
function developerToolsDoLoadProject()
{
	global $mybb, $myCache;

	$phiddle = new PhiddleProject($mybb->input['phiddle']);

	if (!$phiddle->isValid()) {
		exit;
	}

	my_setcookie($cookieKey, $phiddle->get('id'));
	$codeArray[$mybb->user['uid']] = $phiddle->get('content');
	$myCache->update('php_code', $codeArray);

	$data = array(
		'id' => $phiddle->get('id'),
		'title' => $phiddle->get('title'),
		'code' => $codeArray[$mybb->user['uid']],
	);

	// send our headers.
	header('Content-type: application/json');
	echo(json_encode($data));
	exit;
}

/**
 * save the project
 *
 * @param  bool
 * @param  bool
 * @return void
 */
function developerToolsSaveProject($ajax=false, $new=false)
{
	global $mybb, $lang, $html, $myCache, $phiddle;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	if ($ajax && !$new) {
		$phiddle = new PhiddleProject($mybb->input['id']);
	}

	if ($new) {
		$phiddle = new PhiddleProject();
		$phiddle->set('title', $mybb->input['title']);
	}

	$phiddle->set('content', $mybb->input['php_code']);
	$phiddle->save();

	$codeArray = $myCache->read('php_code');

	$codeArray[$mybb->user['uid']] = $mybb->input['php_code'];
	$myCache->update('php_code', $codeArray);

	if (!$ajax) {
		flash_message($lang->developer_tools_success_save_phiddle, 'success');
		admin_redirect($html->url());
	}

	$data = array(
		'id' => $phiddle->get('id'),
		'title' => $phiddle->get('title'),
		'code' => $codeArray[$mybb->user['uid']],
	);

	// send our headers.
	header('Content-type: application/json');
	echo(json_encode($data));
	exit;
}

/**
 * display save as page/modal
 *
 * @param  bool
 * @return void
 */
function developerToolsSaveProjectAs($ajax=false)
{
	global $mybb, $page, $lang, $html, $myCache;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	$codeArray = $myCache->read('php_code');
	$codeArray[$mybb->user['uid']] = $mybb->input['php_code'];
	$myCache->update('php_code', $codeArray);

	if ($ajax) {
		echo <<<EOF
<div class="modal" style="width: 540px;">

EOF;
	} else {
		$page->add_breadcrumb_item($lang->developer_tools_breadcrumb_save);
		$page->output_header("{$lang->developer_tools} &mdash; {$lang->developer_tools_breadcrumb_save}");
	}

	$form = new Form($html->url(array('action' => 'save')), 'post', 'modal_form');
	$formContainer = new FormContainer($lang->developer_tools_breadcrumb_save);

	$formContainer->output_row($lang->developer_tools_phiddle_title_title, $lang->developer_tools_phiddle_title_description, $form->generate_text_box('title', '', array('id' => 'saveAsTitle')).$form->generate_hidden_field('id', $mybb->input['id']).$form->generate_hidden_field('php_code', $mybb->input['php_code']).$form->generate_hidden_field('new', 1));

	$formContainer->end();

	$buttons[] = $form->generate_submit_button($lang->developer_tools_save_button_title, array('name' => 'save_phiddle', 'id' => 'modalSubmit'));
	$buttons[] = $form->generate_submit_button($lang->developer_tools_cancel_button_title, array('name' => 'cancel_save', 'id' => 'modalCancel'));
	$form->output_submit_wrapper($buttons);
	$form->end();

	if ($ajax) {
		echo "\n</div>\n";
	} else {
		$page->output_footer();
	}

	exit;
}

/**
 * display delete page/modal
 *
 * @param  bool
 * @return void
 */
function developerToolsDeleteProject($ajax=false)
{
	global $page, $lang, $html;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	$selectHtml = developerToolsCreatePhiddleSelect('', true);
	if (!$selectHtml) {
		if ($ajax) {
			exit;
		}

		flash_message($lang->developer_tools_error_delete_fail_no_phiddles, 'error');
		admin_redirect($html->url());
	}

	$css = <<<EOF

<style>
select.phiddleList {
	margin: 10px;
	font-size: 1.2em;
}
</style>
EOF;

	if ($ajax) {
		echo <<<EOF
<div class="modal" style="width: 540px;">{$css}

EOF;
	} else {
		$page->extra_header .= $css;
		$page->add_breadcrumb_item($lang->developer_tools_breadcrumb_delete);
		$page->output_header("{$lang->developer_tools} &mdash; {$lang->developer_tools_breadcrumb_delete}");
	}

	$form = new Form($html->url(array('action' => 'doDelete')), 'post', 'modal_form');
	$formContainer = new FormContainer($lang->developer_tools_breadcrumb_delete);

	$formContainer->output_row($lang->developer_tools_phiddle_delete_select_title, $lang->developer_tools_phiddle_delete_select_description, $selectHtml, 'phiddle');

	$formContainer->end();

	$buttons[] = $form->generate_submit_button($lang->developer_tools_delete_button_title, array('name' => 'delete_phiddle', 'id' => 'modalSubmit'));
	$buttons[] = $form->generate_submit_button($lang->developer_tools_cancel_button_title, array('name' => 'cancel_delete', 'id' => 'modalCancel'));
	$form->output_submit_wrapper($buttons);
	$form->end();

	if ($ajax) {
		echo "\n</div>\n";
	} else {
		$page->output_footer();
	}

	exit;
}

/**
 * delete the project(s)
 *
 * @param  bool
 * @return void
 */
function developerToolsDoDeleteProject($ajax=false)
{
	global $lang, $html;

	$errorCount = 0;
	$successCount = 0;
	$deletedIds = array();
	$deletedCurrentProject = false;
	foreach ((array) $mybb->input['phiddle'] as $id) {
		$phiddle = new PhiddleProject($id);

		if (!$phiddle->isValid()) {
			$errorCount++;
			continue;
		}

		$result = $phiddle->remove();

		if (!$result) {
			$errorCount++;
			continue;
		}

		if ($id == $projectId) {
			$deletedCurrentProject = true;
		}

		$deletedIds[] = $id;
		$successCount++;
	}

	if ($deletedCurrentProject) {
		developerToolsNewProject(true);
	}

	if ($ajax) {
		$data = array(
			'deleted' => $successCount,
			'failed' => $errorCount,
			'deletedIds' => $deletedIds,
		);

		// send our headers.
		header('Content-type: application/json');
		echo(json_encode($data));
		exit;
	}

	if ($errorCount) {
		flash_message($lang->sprintf($lang->developer_tools_error_delete_fail_generic, $errorCount), 'error');
	}

	if ($successCount) {
		flash_message($lang->sprintf($lang->developer_tools_success_delete_phiddle_generic, $successCount), 'success');
	}

	admin_redirect($html->url());
}

/**
 * display the import page/modal
 *
 * @param  bool
 * @return void
 */
function developerToolsImportProject($ajax=false)
{
	global $page, $lang, $html;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	if ($ajax) {
		echo <<<EOF
<div class="modal" style="width: 540px;">

EOF;
	} else {
		$page->add_breadcrumb_item($lang->developer_tools_breadcrumb_import);
		$page->output_header("{$lang->developer_tools} &mdash; {$lang->developer_tools_breadcrumb_import}");
	}

	$form = new Form($html->url(array('action' => 'doImport')), 'post', 'modal_form');
	$formContainer = new FormContainer($lang->developer_tools_breadcrumb_import);

	$formContainer->output_row($lang->developer_tools_file_upload_title, $lang->developer_tools_file_upload_description, $form->generate_file_upload_box('file', array('id' => 'fileData')));

	$formContainer->end();

	$buttons[] = $form->generate_submit_button($lang->developer_tools_import_button_title, array('name' => 'import_phiddle', 'id' => 'modalSubmit'));
	$buttons[] = $form->generate_submit_button($lang->developer_tools_cancel_button_title, array('name' => 'cancel_import', 'id' => 'modalCancel'));
	$form->output_submit_wrapper($buttons);
	$form->end();

	if ($ajax) {
		echo "\n</div>\n";
	} else {
		$page->output_footer();
	}

	exit;
}

/**
 * import the project
 *
 * @param  bool
 * @return void
 */
function developerToolsDoImportProject($ajax = false)
{
	global $lang, $html;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	$xml = developerToolsCheckUploadedFile('file', '', $ajax);

	$phiddle = new PhiddleProject();
	$result = $phiddle->import($xml);
	if (!$result) {
		if ($ajax) {
			exit;
		}

		flash_message($lang->developer_tools_error_import_fail, 'error');
		admin_redirect($html->url());
	}

	$id = $phiddle->save($xml);
	if (!$id) {
		if ($ajax) {
			exit;
		}

		flash_message($lang->developer_tools_error_import_fail, 'error');
		admin_redirect($html->url());
	}

	if ($ajax) {
		$data = array(
			'success' => true,
		);

		// send our headers.
		header('Content-type: application/json');
		echo(json_encode($data));
		exit;
	}

	flash_message($lang->developer_tools_success_import_phiddle, 'success');
	admin_redirect($html->url());
}

/**
 * preview the project
 *
 * @param  bool
 * @return void
 */
function developerToolsPreviewProject($ajax=false)
{
	global $mybb, $config, $lang, $html, $myCache;

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	$userCode = $mybb->input['php_code'];
	$codeArray[$mybb->user['uid']] = $userCode;
	$myCache->update('php_code', $codeArray);

	developerToolsWriteTemp($userCode);

	if ($ajax) {
		$data = array(
			'url' => "{$mybb->settings['bburl']}/{$config['admin_dir']}/modules/developer_tools/sandbox/{$mybb->user['uid']}/index.php",
		);

		// send our headers.
		header('Content-type: application/json');
		echo(json_encode($data));
		exit;
	}

	flash_message($lang->developer_tools_success_preview, 'success');
	admin_redirect($html->url(array('action' => 'execute')).'#output');
}

/**
 * build the project select HTML
 *
 * @param  string
 * @param  bool
 * @return void
 */
function developerToolsCreatePhiddleSelect($selected = '', $multi=false)
{
	global $db, $lang;

	$form = new Form('', '', '', false, '', true);

	if (!$lang->developer_tools) {
		$lang->load('developer_tools');
	}

	$query = $db->simple_select('phiddles', 'id,title');
	$count = $db->num_rows($query);
	if ($count == 0) {
		return false;
	}

	while ($phiddle = $db->fetch_array($query)) {
		$options[$phiddle['id']] = $phiddle['title'];
	}

	$size = 10;
	if ($count < 10) {
		$size = $count;
	}

	$attr = array('id' => 'phiddle_select', 'size' => $size, 'class' => 'phiddleList');

	$name = 'phiddle';
	if ($multi) {
		$attr['multiple'] = true;
		$name .= '[]';
	}

	return $form->generate_select_box($name, $options, $selected, $attr);
}

/**
 * validate an uploaded file and return its contents
 *
 * @param  string the name of the file input
 * @param  string the redirect URL on error
 * @return string the file contents
 */
function developerToolsCheckUploadedFile($name = 'file', $returnUrl = '', $ajax=false)
{
	global $lang, $html;

	if (!$returnUrl) {
		$returnUrl = $html->url();
	}

	if (!$_FILES[$name] ||
		$_FILES[$name]['error'] == 4) {
		if ($ajax) {
			exit;
		}

		flash_message('no file', 'error');
		admin_redirect($returnUrl);
	}

	if ($_FILES[$name]['error']) {
		if ($ajax) {
			exit;
		}

		flash_message($lang->sprintf($lang->developer_tools_error_file_upload_generic, $_FILES['file']['error']), 'error');
		admin_redirect($returnUrl);
	}

	if (!is_uploaded_file($_FILES[$name]['tmp_name'])) {
		if ($ajax) {
			exit;
		}

		flash_message($lang->developer_tools_error_file_upload_fail, 'error');
		admin_redirect($returnUrl);
	}

	$content = @file_get_contents($_FILES[$name]['tmp_name']);
	@unlink($_FILES[$name]['tmp_name']);

	if (strlen(trim($content)) == 0) {
		if ($ajax) {
			exit;
		}

		flash_message($lang->developer_tools_error_file_upload_file_empty, 'error');
		admin_redirect($returnUrl);
	}

	return $content;
}

?>
