<?php

define('DEV_TOOLS_DEFAULT_TITLE', '[New PHiddle]');

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
$urlExtra = '-phiddle';
if ($action) {
	$urlExtra = "-{$action}";
}

// URL, link and image markup generator
$html = new HTMLGenerator010000(DEVELOPER_TOOLS_URL . $urlExtra);

$modules = developerToolsGetAllModules();
$moduleActions = array_keys($modules);

if (!in_array($page->active_action, $moduleActions)) {
	developerToolsPHiddle();
	exit;
}

$page->add_breadcrumb_item($lang->developer_tools);

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
function developerToolsPHiddle()
{
	global $config, $mybb, $db, $page, $html, $lang, $cp_style, $phiddle, $myCache;

	$myCache = DeveloperToolsCache::getInstance();

	require_once MYBB_ROOT . 'inc/plugins/developer_tools/functions_phiddle.php';

	$title = DEV_TOOLS_DEFAULT_TITLE;
	$cookieKey = "phiddle_project{$mybb->user['uid']}";
	$phpCode = ' ';
	$projectId = (int) $mybb->cookies[$cookieKey];
	if ($projectId > 0) {
		$phiddle = new PhiddleProject($projectId);
		if ($phiddle->isValid()) {
			$title = $phiddle->get('title');
			$phpCode = $phiddle->get('content');
		}
	} else {
		$phiddle = new PhiddleProject();
	}

	if ($mybb->input['mode'] == 'ajax') {
		developerToolsXmlhttp();
		exit;
	}

	$codeArray = $myCache->read('php_code');

	if (!empty($codeArray[$mybb->user['uid']])) {
		$phpCode = $codeArray[$mybb->user['uid']];
	}

	if ($mybb->request_method == 'post') {
		if (isset($mybb->input['newButton'])) {
			developerToolsNewProject();

			flash_message('Project code cleared.', 'success');
			admin_redirect($html->url());
		} elseif (isset($mybb->input['loadButton'])) {
			developerToolsLoadProject();
		} elseif (isset($mybb->input['saveButton'])) {
			if ($phiddle->isValid()) {
				developerToolsSaveProject();
			} else {
				developerToolsSaveProjectAs();
			}
		} elseif (isset($mybb->input['save_phiddle'])) {
			if ($phiddle->isValid()) {
				$phiddle->set('id', 0);
			}

			$phiddle->set('content', $phpCode);
			$phiddle->set('title', $mybb->input['title']);
			$id = $phiddle->save();

			if (!$id) {
				flash_message('Phiddle could not be saved successfully', 'error');
				admin_redirect($html->url());
			}

			my_setcookie($cookieKey, $id);

			flash_message('Phiddle saved successfully', 'success');
			admin_redirect($html->url());
		} elseif (isset($mybb->input['saveAsButton'])) {
			developerToolsSaveProjectAs();
		} elseif (isset($mybb->input['deleteButton'])) {
			developerToolsDeleteProject();
		} elseif (isset($mybb->input['previewButton'])) {
			developerToolsPreviewProject();
		} elseif (isset($mybb->input['load_phiddle'])) {
			$phiddle = new PhiddleProject($mybb->input['phiddle']);

			if (!$phiddle->isValid()) {
				flash_message('PHiddle could not be loaded', 'success');
				admin_redirect($html->url());
			}

			my_setcookie($cookieKey, $phiddle->get('id'));
			$codeArray[$mybb->user['uid']] = $phiddle->get('content');
			$myCache->update('php_code', $codeArray);
			
			flash_message('PHiddle successfully loaded.', 'success');
			admin_redirect($html->url());
		} elseif (isset($mybb->input['delete_phiddle'])) {
			developerToolsDoDeleteProject();
		} elseif (isset($mybb->input['importButton'])) {
			developerToolsImportProject();
		} elseif (isset($mybb->input['import_phiddle'])) {
			developerToolsDoImportProject();
		} elseif (isset($mybb->input['exportButton'])) {
			if (!$projectId) {
				flash_message('PHiddles must be saved before they can be exported.', 'error');
				admin_redirect($html->url());
			}
			$phiddle->export();
			exit;
		}
	}

	$iframeSource = '';
	if ($mybb->input['action'] == 'execute') {
		$iframeSource = "{$mybb->settings['bburl']}/{$config['admin_dir']}/modules/developer_tools/sandbox/{$mybb->user['uid']}/index.php";
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
	<script type="text/javascript">
	<!--
	DevTools.PHiddle.setup({
		uid: "{$mybb->user['uid']}",
		id: "{$id}",
	}, {});
	// -->
	</script>

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

input.newButton {
	background: url(./styles/{$cp_style}/images/developer_tools/new.gif);
}

input.loadButton {
	background: url(./styles/{$cp_style}/images/developer_tools/load.gif);
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

input.deleteButton {
	background: url(./styles/{$cp_style}/images/developer_tools/delete.gif);
}

input.importButton {
	background: url(./styles/{$cp_style}/images/developer_tools/import.png);
}

input.exportButton {
	background: url(./styles/{$cp_style}/images/developer_tools/export.png);
}

input.previewButton {
	background: url(./styles/{$cp_style}/images/developer_tools/preview.gif);
	float: right;
	margin-right: 6px;
}
</style>
EOF;

	$page->add_breadcrumb_item($lang->developer_tools_admin_home);
	$page->output_header($lang->developer_tools_admin_home . " &mdash; {$title}");

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
				<input type="submit" value=" " id="importButton" name="importButton" class="toolbarButton importButton" title="Import..."/>
				<input type="submit" value=" " id="exportButton" name="exportButton" class="toolbarButton exportButton" title="Export"/>
				<input type="submit" value=" " id="previewButton" name="previewButton" class="toolbarButton previewButton" title="Preview"/>
				<input type="hidden" id="hiddenId" name="id"/>
			</span>
		</div>
EOF;

	echo($form->generate_text_area('php_code', $phpCode | ' ', array('rows' => 11, "columns" => 145, 'id' => 'php_code')));

	echo <<<EOF

	</div>
	<div id="qt_body_main_output" name="output" class="quick_tab">
		<iframe id="output_frame" src="{$iframeSource}" class="outputFrame"> </iframe>
	</div>
EOF;

	$form->end();

	echo '</div>';
	$page->output_footer();	
}

function developerToolsXmlhttp()
{
	global $mybb;

	$new = false;
	if (isset($mybb->input['new'])) {
		$new = true;
	}

	switch ($mybb->input['action']) {
	case 'new':
		developerToolsNewProject();
		break;
	case 'load':
		developerToolsLoadProject(true);
		break;
	case 'doLoad':
		developerToolsDoLoadProject();
		break;
	case 'save':
		developerToolsSaveProject(true, $new);
		break;
	case 'saveAs':
		developerToolsSaveProjectAs(true);
		break;
	case 'delete':
		developerToolsDeleteProject(true);
		break;
	case 'doDelete':
		developerToolsDoDeleteProject(true);
		break;
	case 'import':
		developerToolsImportProject(true);
		break;
	case 'doImport':
		developerToolsDoImportProject(true);
		break;
	case 'preview':
		developerToolsPreviewProject(true);
		break;
	}
}

?>
