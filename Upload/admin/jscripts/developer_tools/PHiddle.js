var DevTools = (function($, dt) {
	"use strict";

	var Editor,
		tabs,
		url = "index.php?module=developer_tools-phiddle",

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
		$("#loadButton").click(loadOnClick);

		tabs.show(activeTab);
	}

	function newOnClick(e) {
		e.preventDefault();

		$.ajax({
			type: "post",
			url: url,
			data: {
				action: "new",
				mode: "ajax",
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

	function loadOnClick(e) {
		e.preventDefault();

		$.get(url+"&mode=ajax&action=load", function(html) {
			$(html).appendTo("body").modal({
				fadeDuration: 250,
				zIndex: (typeof modal_zindex !== "undefined" ? modal_zindex : 9999),
			});

			$("#modalSubmit").click(loadOnSubmit);
		});
	}

	function loadOnSubmit(e) {
		e.preventDefault();

		$("#modalSubmit").off("click", loadOnSubmit);

		$.ajax({
			type: "post",
			url: $("#modal_form").attr("action") + "&mode=ajax",
			data: $("#modal_form").serialize(),
			success: function(data) {
				$(data).filter("script").each(function(e) {
					eval($(this).text());
				});
				$.modal.close();
			},
			success: loadOnSuccess,
			error: xmlhttpError,
		});
	}

	function loadOnSuccess(data) {
		Editor.setValue(data.code);
		setPageTitle(data.title);
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
		Editor: Editor,
	};

	return dt;
})(jQuery, DevTools || {});
