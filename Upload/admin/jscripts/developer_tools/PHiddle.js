var DevTools = (function($, dt) {
	"use strict";

	var Editor,

	options = {
	},

	lang = {
	};

	function setup(o, l) {
		$.extend(options, o || {});
		$.extend(lang, l || {});
	}

	function init() {
		var reFocus = false;
		if (location.hash == "#tab_output") {
			reFocus = true;
		}

		$("#tabs li.first").find('a').click();

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

		if (reFocus) {
			$("#tabs li.first").next('li').find('a').click();
		}
	}

	$(init);

	return dt;
})(jQuery, DevTools || {});
