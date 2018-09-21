var DevTools = (function($, dt) {
	"use strict";

	var Editor,
		tabs,
		url = "index.php?module=developer_tools-phiddle",
		projectId = 0,
		projectTitle = "",
		cookieKey = "",

	options = {
		uid: 0,
	},

	lang = {
		phiddle_deleted: 'Phiddle deleted.',
		phiddles_deleted: 'Phiddles deleted.',
	};

	function setup(o, l) {
		$.extend(options, o || {});
		$.extend(lang, l || {});

		projectId = parseInt(options.id, 10);
		cookieKey = "phiddle_project"+options.uid;
	}

	function init() {
		var activeTab;

		DevTools.QuickTab.newInstance('main', ['php','output']);
		tabs = DevTools.QuickTab.getInstance('main');

		tabs.getActive();
		activeTab = tabs.active;

		tabs.show("php");
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
		$("#saveButton").click(saveOnClick);
		$("#saveAsButton").click(saveAsOnClick);
		$("#deleteButton").click(deleteOnClick);

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

		$.jGrowl("PHiddle cleared.", {theme: "jgrowl_success"});
	}

	function loadOnClick(e) {
		e.preventDefault();

		$.get(url+"&mode=ajax&action=load", function(html) {
			$(html).appendTo("body").modal({
				fadeDuration: 250,
				zIndex: (typeof modal_zindex !== "undefined" ? modal_zindex : 9999),
			});

			$("#modalSubmit").one("click", loadOnSubmit);
			$("#modalCancel").one("click", cancelOnClick);
		});
	}

	function loadOnSubmit(e) {
		e.preventDefault();

		$.ajax({
			type: "post",
			url: $("#modal_form").attr("action") + "&mode=ajax",
			data: $("#modal_form").serialize(),
			success: loadOnSuccess,
			error: xmlhttpError,
		});
	}

	function loadOnSuccess(data) {
		$.modal.close();
		projectId = data.id;
		Editor.setValue(data.code);
		setPageTitle(data.title);
		Cookie.set(cookieKey, projectId);
		$.jGrowl("PHiddle loaded.", {theme: "jgrowl_success"});
	}

	function saveOnClick(e) {
		if (!projectId) {
			saveAsOnClick(e);
		}

		e.preventDefault();

		$.ajax({
			type: "post",
			url: url,
			data: {
				action: "save",
				mode: "ajax",
				id: projectId,
				title: projectTitle,
				php_code: Editor.getValue(),
			},
			success: saveOnSuccess,
			error: xmlhttpError,
		});
	}

	function saveOnSuccess(data) {
		$.jGrowl("PHiddle saved.", {theme: "jgrowl_success"});
	}

	function saveAsOnClick(e) {
		e.preventDefault();

		$.ajax({
			type: "post",
			url: url,
			data: {
				action: "saveAs",
				mode: "ajax",
				php_code: Editor.getValue(),
			},
			success: function(html) {
				$(html).appendTo("body").modal({
					fadeDuration: 250,
					zIndex: (typeof modal_zindex !== "undefined" ? modal_zindex : 9999),
				});

				$("#modalSubmit").one("click", saveAsOnSubmit);
				$("#modalCancel").one("click", cancelOnClick);
			},
			error: xmlhttpError,
		});
	}

	function saveAsOnSubmit(e) {
		e.preventDefault();

		$.ajax({
			type: "post",
			url: $("#modal_form").attr("action") + "&mode=ajax",
			data: $("#modal_form").serialize(),
			success: saveAsOnSuccess,
			error: xmlhttpError,
		});
	}

	function saveAsOnSuccess(data) {
		projectId = data.id;
		setPageTitle(data.title);
		Cookie.set(cookieKey, projectId);
		$.jGrowl("PHiddle saved.", {theme: "jgrowl_success"});
		$.modal.close();
	}

	function deleteOnClick(e) {
		e.preventDefault();

		$.get(url+"&mode=ajax&action=delete", function(html) {
			$(html).appendTo("body").modal({
				fadeDuration: 250,
				zIndex: (typeof modal_zindex !== "undefined" ? modal_zindex : 9999),
			});

			$("#modalSubmit").one("click", deleteOnSubmit);
			$("#modalCancel").one("click", cancelOnClick);
		});
	}

	function deleteOnSubmit(e) {
		e.preventDefault();

		$.ajax({
			type: "post",
			url: $("#modal_form").attr("action") + "&mode=ajax",
			data: $("#modal_form").serialize(),
			success: deleteOnSuccess,
			error: xmlhttpError,
		});
	}

	function deleteOnSuccess(data) {
		var successLanguage = "PHiddle successfully deleted.",
			errorLanguage = "PHiddle could not be deleted successfully.";

		if (data.deleted > 0 &&
			data.deletedIds.length &&
			data.deletedIds.indexOf(projectId) != -1) {
			clear(true);
		}

		$.modal.close();

		if (data.deleted > 0) {
			if (data.deleted > 1) {
				successLanguage = "PHiddles successfully deleted.";
			}
			$.jGrowl(data.deleted+" "+successLanguage, {theme: "jgrowl_success"});
		}

		if (!data.failed) {
			return;
		}

		if (data.failed > 1) {
			errorLanguage = "PHiddles could not be deleted successfully.";
		}
		$.jGrowl(data.failed+" "+errorLanguage, {theme: "jgrowl_error"});
	}

	function cancelOnClick(e) {
		e.preventDefault();

		$.modal.close();
	}

	function clear(keepCode) {
		if (!keepCode) {
			Editor.setValue("");
		}

		Cookie.unset(cookieKey);
		setPageTitle();
		projectId = 0;
	}

	function setPageTitle(title) {
		if (!title) {
			projectTitle = '';
			title = '[New PHiddle]';
		} else {
			projectTitle = title;
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
