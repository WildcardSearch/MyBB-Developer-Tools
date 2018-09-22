var DevTools = (function($, dt) {
	"use strict";

	var Editor,
		tabs,
		url = "index.php?module=developer_tools-phiddle",
		projectId = 0,
		projectTitle = "",
		cookieKey = "",
		hasChanged = false,
		mirror = "",

	options = {
		uid: 0,
	},

	lang = {
		success_code_cleared: "Project code cleared.",
		success_load_generic: "PHiddle successfully loaded.",
		success_save_phiddle: "Phiddle saved successfully.",
		error_delete_fail_generic: "{1} PHiddle(s) could not be successfully deleted.",
		success_delete_phiddle_generic: "{1} PHiddle(s) successfully deleted.",
		success_import_phiddle: "PHiddle successfully imported.",
		error_import_fail: "PHiddle could not be imported successfully.",
		success_preview: "PHP code successfully executed.",
		default_title: "[New PHiddle]",
		phiddle: "PHiddle",
	};

	function setup(o, l) {
		$.extend(options, o || {});
		$.extend(lang, l || {});

		projectId = parseInt(options.id, 10);
		cookieKey = "phiddle_project"+options.uid;
	}

	function init() {
		var activeTab;

		DevTools.QuickTab.newInstance("main", ["php","output"]);
		tabs = DevTools.QuickTab.getInstance("main");

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

		mirror = Editor.getValue();

		Editor.addPanel($("#toolBarContainer")[0]);
		Editor.on("change", editorChanged);

		$("#newButton").click(newOnClick);
		$("#loadButton").click(loadOnClick);
		$("#saveButton").click(saveOnClick);
		$("#saveAsButton").click(saveAsOnClick);
		$("#deleteButton").click(deleteOnClick);
		$("#importButton").click(importOnClick);
		$("#previewButton").click(previewOnClick);

		tabs.show(activeTab);

		$(window).on("beforeunload", windowUnload);
	}

	function windowUnload(e) {
		if (hasChanged) {
			return true;
		}
	}

	function editorChanged(e) {
		if (Editor.getValue() !== mirror) {
			$("#saveButton").prop("disabled", false);
			hasChanged = true;
			return;
		}

		$("#saveButton").prop("disabled", true);
		hasChanged = false;
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

		$.jGrowl(lang.success_code_cleared, {theme: "jgrowl_success"});
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
		mirror = data.code;
		setPageTitle(data.title);
		Cookie.set(cookieKey, projectId);
		hasChanged = false;
		$.jGrowl(lang.success_load_generic, {theme: "jgrowl_success"});
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
		hasChanged = false;
		mirror = Editor.getValue();
		$.jGrowl(lang.success_save_phiddle, {theme: "jgrowl_success"});
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
		$.jGrowl(lang.success_save_phiddle, {theme: "jgrowl_success"});
		hasChanged = false;
		mirror = Editor.getValue();
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
		if (data.deleted > 0 &&
			data.deletedIds.length &&
			data.deletedIds.indexOf(projectId) != -1) {
			clear(true);
		}

		$.modal.close();

		if (data.deleted > 0) {
			$.jGrowl(lang.success_delete_phiddle_generic.replace("{1}", data.deleted), {theme: "jgrowl_success"});
		}

		if (!data.failed) {
			return;
		}

		$.jGrowl(lang.error_delete_fail_generic.replace("{1}", data.failed), {theme: "jgrowl_error"});
	}

	function importOnClick(e) {
		e.preventDefault();

		$.get(url+"&mode=ajax&action=import", function(html) {
			$(html).appendTo("body").modal({
				fadeDuration: 250,
				zIndex: (typeof modal_zindex !== "undefined" ? modal_zindex : 9999),
			});

			$("#modalSubmit").one("click", importOnSubmit);
			$("#modalCancel").one("click", cancelOnClick);
		});
	}

	function importOnSubmit(e) {
		var data = new FormData();

		e.preventDefault();

		data.append('file', $("#fileData").prop("files")[0]);

		$.ajax({
			type: "post",
			url: $("#modal_form").attr("action") + "&mode=ajax",
			cache: false,
			contentType: false,
			processData: false,
			data: data,
			success: importOnSuccess,
			error: xmlhttpError,
		});
	}

	function importOnSuccess(data) {
		$.modal.close();

		if (data.success) {
			$.jGrowl(lang.success_import_phiddle, {theme: "jgrowl_success"});
		} else {
			$.jGrowl(lang.error_import_fail, {theme: "jgrowl_error"});
		}
	}

	function previewOnClick(e) {
		e.preventDefault();

		$.ajax({
			type: "post",
			url: url,
			data: {
				action: "preview",
				mode: "ajax",
				php_code: Editor.getValue(),
			},
			success: previewOnSuccess,
			error: xmlhttpError,
		});
	}

	function previewOnSuccess(data) {
		tabs.show("output");
		$("#output_frame").prop("src", data.url);

		$.jGrowl(lang.success_preview, {theme: "jgrowl_success"});
	}

	function cancelOnClick(e) {
		e.preventDefault();

		$.modal.close();
	}

	function clear(keepCode) {
		if (!keepCode) {
			Editor.setValue("");
			mirror = "";
			hasChanged = false;
		} else {
			hasChanged = true;
		}

		Cookie.unset(cookieKey);
		setPageTitle();
		projectId = 0;
	}

	function setPageTitle(title) {
		if (!title) {
			projectTitle = '';
			title = lang.default_title;
		} else {
			projectTitle = title;
		}

		document.title = lang.phiddle+" — "+title;
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
