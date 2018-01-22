<?php

// Disallow direct access to this file for security reasons
if (!defined("IN_MYBB")) {
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

global $page, $mybb, $lang, $html, $min, $modules;

if (!$lang->developer_tools) {
	$lang->load('developer_tools');
}

if ($mybb->settings['developer_tools_minify_js']) {
	$min = '.min';
}

$action = $page->active_action;
$urlExtra = '';
if ($action) {
	$urlExtra = "-{$action}";
}

// URL, link and image markup generator
$html = new HTMLGenerator010000(DEVELOPER_TOOLS_URL . $urlExtra, array('ajax'));

$modules = developerToolsGetAllModules();
$moduleActions = array_keys($modules);

$page->add_breadcrumb_item($lang->developer_tools);

if (!in_array($page->active_action, $moduleActions)) {
	developer_tools_PHiddle();
	exit;
}

$module = $modules[$action];
if (!$module->isValid()) {
	flash_message('Invalid module.', 'error');
	admin_redirect($html(array()));
}

if ($mybb->request_method == 'post') {
	if (!$module->get('hasSettings')) {
		$module->execute();
	}

	$options = array();
	foreach ($module->get('settings') as $name => $setting) {
		$options[$name] = $setting['value'];
		if (isset($mybb->input[$name])) {
			$options[$name] = $mybb->input[$name];
		}
	}

	$module->execute($options);
}

$page->extra_header .= <<<EOF
	<style>
		div.infoTitle {
			font-weight: bold;
			font-size: 1.5em;
			text-shadow: 1px 1px 2px grey;
		}

		div.infoDescription {
			font-style: italic;
		}
	</style>

EOF;

$page->add_breadcrumb_item($module->get('title'));

$page->output_header("{$lang->developer_tools} &mdash; {$module->get('title')}");

$longDescription = '';
if ($module->get('longDescription')) {
	$longDescription = <<<EOF

		<div style="width: 75%; padding: 0px 10px; margin: 5px auto 5px auto; border-radius: 3px; border: 1px solid black; background: #F5F5F5; font-size: 1.2em;">{$module->get('longDescription')}</div>
EOF;
}

echo <<<EOF
	<div class="form_button_wrapper">
		<div class="infoTitle">
			<span>{$module->get('title')}</span>
		</div>
		<div class="infoDescription">
			<span>{$module->get('description')}</span>
		</div>{$longDescription}
	</div>
	<br />
EOF;

$form = new Form($html->url(), 'post');

if ($module->get('hasSettings')) {
	$formTitle = $lang->sprintf($lang->developer_tools_module_execute_form_title, $module->get('title'));
	$formContainer = new FormContainer($formTitle);

	$module->outputSettings($formContainer);

	$formContainer->end();
}

$buttons[] = $form->generate_submit_button($lang->developer_tools_module_execute, array('name' => 'execute_module'));
$form->output_submit_wrapper($buttons);
$form->end();

$page->output_footer();
exit;

/**
 * display PHiddle page
 *
 * @return void
 */
function developer_tools_PHiddle()
{
	global $config, $mybb, $page, $html, $lang, $cp_style;

	$myCache = DeveloperToolsCache::getInstance();
	$codeArray = $myCache->read('php_code');

	$phpCode = ' ';
	if (!empty($codeArray[$mybb->user['uid']])) {
		$phpCode = $codeArray[$mybb->user['uid']];
	}

	if ($mybb->request_method == 'post') {
		$userCode = $mybb->input['php_code'];
		$codeArray[$mybb->user['uid']] = $userCode;
		$myCache->update('php_code', $codeArray);

		$code = <<<EOF
<?php

define('IN_MYBB', 1);
define('NO_ONLINE', 1);
require_once '../../../../global.php';

{$userCode}

?>

EOF;
		file_put_contents(DEVELOPER_TOOLS_SANDBOX_FILE_PATH, $code);

		flash_message('PHP code successfully executed.', 'success');
		admin_redirect($html->url(array('action' => 'execute')) . '#output');
	}

	$iframeSource = '';
	if ($mybb->input['action'] == 'execute') {
		$iframeSource = "{$mybb->settings['bburl']}/{$config['admin_dir']}/modules/developer_tools/sandbox/index.php";
	}

	$page->extra_header .= <<<EOF

	<link href="./jscripts/codemirror/lib/codemirror.css?ver=1813" rel="stylesheet">
	<link href="./jscripts/developer_tools/codemirror/theme/blackboard.css" rel="stylesheet">
	<link href="./jscripts/developer_tools/codemirror/addon/display/fullscreen.css" rel="stylesheet">
	<link href="./styles/default/developer_tools/tabs.css" rel="stylesheet">

	<link href="./styles/{$cp_style}/developer_tools/tabs.css" rel="stylesheet">
	<link href="./styles/{$cp_style}/developer_tools/global.css" rel="stylesheet">

	<script src="./jscripts/codemirror/lib/codemirror.js?ver=1813"></script>
	<script src="./jscripts/developer_tools/codemirror/mode/clike/clike.js"></script>
	<script src="./jscripts/developer_tools/codemirror/mode/php/php.js"></script>
	<script src="./jscripts/developer_tools/codemirror/addon/edit/matchbrackets.js"></script>
	<script src="./jscripts/developer_tools/codemirror/addon/edit/closebrackets.js"></script>
	<script src="./jscripts/codemirror/addon/search/match-highlighter.js"></script>
	<script src="./jscripts/developer_tools/codemirror/addon/comment/continuecomment.js"></script>
	<script src="./jscripts/developer_tools/codemirror/addon/display/fullscreen.js"></script>
	<script src="./jscripts/developer_tools/codemirror/addon/display/panel.js"></script>

	<script src="./jscripts/developer_tools/tabs.js"></script>
	<script src="./jscripts/developer_tools/PHiddle.js"></script>

<style>
/* toolbar */

#toolBarContainer {
	background: lightgrey;
	max-width: 100%;
	width: auto;
	margin: auto;
	padding: 7px 0px 3px 5px;
	font-size: 12px;
}

input.toolbarButton {
	height: 34px;
	width: 34px;
	cursor: pointer;
}

input.deleteButton {
	background: url(./styles/{$cp_style}/images/developer_tools/delete.gif);
}

input.loadButton {
	background: url(./styles/{$cp_style}/images/developer_tools/load.gif);
}

input.newButton {
	background: url(./styles/{$cp_style}/images/developer_tools/new.gif);
}

input.saveButton {
	background: url(./styles/{$cp_style}/images/developer_tools/save.gif);
}

input.saveButton:disabled {
	background: url(./styles/{$cp_style}/images/developer_tools/save_disabled.gif);
	cursor: default;
}

input.saveAsButton {
	background: url(./styles/{$cp_style}/images/developer_tools/saveas.gif);
}

input.previewButton {
	background: url(./styles/{$cp_style}/images/developer_tools/preview.gif);
	float: right;
	margin-right: 6px;
}
</style>
EOF;

	$page->add_breadcrumb_item($lang->developer_tools_admin_home);
	$page->output_header($lang->developer_tools_admin_home);

	echo <<<EOF
	<div id="quick_tab_main">
		<li id="qt_link_main_php" name="php" class="quick_tab">
			<a href="{$html->url()}#php">PHP</a>
			<span style="display: none;">PHP</span>
		</li>
		<li id="qt_link_main_output" name="output" class="quick_tab">
			<a href="{$html->url()}#output">Output</a>
			<span style="display: none;">Output</span>
		</li>
EOF;

	$form = new Form($html->url(), 'post');

	echo <<<EOF

	<div id="qt_body_main_php" name="php" class="quick_tab">
		<div id="toolBarContainer">
			<span id="toolBar" class="toolBar">
				<input type="submit" value=" " id="newButton" name="newButton" class="toolbarButton newButton" title="New"/>
				<input type="submit" value=" " id="loadButton" name="loadButton" class="toolbarButton loadButton" title="Load..."/>
				<input type="submit" value=" " id="saveButton" name="saveButton" class="toolbarButton saveButton" title="Save"/>
				<input type="submit" value=" " id="saveAsButton" name="saveAsButton" class="toolbarButton saveAsButton" title="Save As..."/>
				<input type="submit" value=" " id="deleteButton" name="deleteButton" class="toolbarButton deleteButton" title="Delete..."/>
				<input type="submit" value=" " id="previewButton" name="previewButton" class="toolbarButton previewButton" title="Preview"/>
				<input type="hidden" id="hiddenId" name="id"/>
			</span>
		</div>
EOF;

	echo($form->generate_text_area('php_code', $phpCode | ' ', array('rows' => 11, "columns" => 145, 'id' => 'php_code')));

	echo <<<EOF

	</div>
	<div id="qt_body_main_output" name="output" class="quick_tab">
		<iframe src="{$iframeSource}" class="outputFrame"> </iframe>
	</div>
EOF;

	$buttons[] = $form->generate_submit_button($lang->developer_tools_module_execute, array('name' => 'execute_php'));
	$form->output_submit_wrapper($buttons);
	$form->end();

	echo '</div>';
	$page->output_footer();	
}

?>
