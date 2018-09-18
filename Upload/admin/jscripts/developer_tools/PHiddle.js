var DevTools = (function($, dt) {
	"use strict";

	var Editor,
		tabs,

	options = {
		uid: 0,
	},

	lang = {
	};

	function setup(o, l) {
		$.extend(options, o || {});
		$.extend(lang, l || {});
	}

	function init() {
		var activeTab;

		DevTools.QuickTab.newInstance('main', ['php','output']);
		tabs = DevTools.QuickTab.getInstance('main');

		tabs.getActive();
		activeTab = tabs.active;

		tabs.show('php');
		Editor = CodeMirror.fromTextArea($("#php_code")[0], {
			mode: "text/x-php",
			theme: "blackboard",
			autofocus: true,
			lineNumbers: true,
			firstLineNumber: 9,
			lineWrapping: true,
			tabMode: "indent",
			indentUnit: 4,
			indentWithTabs: true,
			matchBrackets: true,
			autoCloseBrackets: true,
			highlightSelectionMatches: true,
			continueComments: {
				continueLineComment: false,
			},
			extraKeys: {
				"F11": function(cm) {
					cm.setOption("fullScreen", !cm.getOption("fullScreen"));
					//$windowToggle.toggle();
				},
				"Esc": function(cm) {
					if (cm.getOption("fullScreen")) {
						cm.setOption("fullScreen", false);
					}
				}
			},
		});

		if (Editor.getValue() === " ") {
			Editor.setValue("");
		}

		Editor.addPanel($("#toolBarContainer")[0]);

		$("#newButton").click(newOnClick);

		tabs.show(activeTab);
	}

	function newOnClick(e) {
		e.preventDefault();

		$.ajax({
			type: "post",
			url: "modules/developer_tools/xmlhttp.php",
			data: {
				action: "new",
				mode: "phiddle",
			},
			success: newOnSuccess,
			error: xmlhttpError,
		});
	}

	function newOnSuccess() {
		clear();

		Cookie.unset('phiddle_project'+options.uid);
		$.jGrowl("PHiddle cleared.", {theme: "jgrowl_success"});
	}

	function clear() {
		Editor.setValue("");
		setPageTitle();
	}

	function setPageTitle(title) {
		if (!title) {
			title = '[New PHiddle]';
		}

		document.title = "PHiddle — "+title;
	}

	function xmlhttpError(jqXHR, textStatus, errorThrown) {
		console.log(jqXHR);
		$.jGrowl(textStatus+": <br /><br />" + errorThrown, {theme: "jgrowl_error"});
	}

	$(init);

	dt.PHiddle = {
		setup: setup,
	};

	return dt;
})(jQuery, DevTools || {});
