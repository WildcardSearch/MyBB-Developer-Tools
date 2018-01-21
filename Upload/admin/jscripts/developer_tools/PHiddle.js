var DevTools = (function($, dt) {
	"use strict";

	var Editor,
		tabs,

	options = {
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
			firstLineNumber: 7,
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

		tabs.show(activeTab);
	}

	$(init);

	return dt;
})(jQuery, DevTools || {});
