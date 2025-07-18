/**
 * Ace editor field
 *
 * Autor: slinkybass
 * Version: 3.0
 */

import ace from "ace-builds/src-min-noconflict/ace";

const CDN = "https://cdn.jsdelivr.net/npm/ace-builds@latest/src-min-noconflict";
ace.config.set("basePath", CDN);
ace.config.set("modePath", CDN);
ace.config.set("themePath", CDN);
ace.config.set("workerPath", CDN);

(function () {
	document.addEventListener("DOMContentLoaded", () => {
		formTypeCodeEditor();
	});
	document.addEventListener("ea.collection.item-added", () => {
		formTypeCodeEditor();
	});

	window.formTypeCodeEditor = function formTypeCodeEditor(selector = '[data-codeeditor-field="true"]') {
		document.querySelectorAll(selector).forEach((e) => {
			const theme = e.hasAttribute("data-codeeditor-theme") ? e.getAttribute("data-codeeditor-theme") : "chrome";
			const language = e.hasAttribute("data-codeeditor-language") ? e.getAttribute("data-codeeditor-language") : "javascript";
			const tabSize = e.hasAttribute("data-codeeditor-tab-size") ? e.getAttribute("data-codeeditor-tab-size") : 4;
			const indentWithTabs = e.hasAttribute("data-codeeditor-indent-with-tabs") ? e.getAttribute("data-codeeditor-indent-with-tabs") !== "false" : true;
			const showLineNumbers = e.hasAttribute("data-codeeditor-show-line-numbers") ? e.getAttribute("data-codeeditor-show-line-numbers") !== "false" : true;
			const minLines = e.hasAttribute("data-codeeditor-min-lines") ? e.getAttribute("data-codeeditor-min-lines") : 5;
			const maxLines = e.hasAttribute("data-codeeditor-max-lines") ? e.getAttribute("data-codeeditor-max-lines") : 20;

			// Clone and hide the original field
			const clonedE = e.cloneNode(true);
			clonedE.classList.add("d-none");
			e.parentNode.insertBefore(clonedE, e);

			const editor = ace.edit(e, {
				theme: "ace/theme/" + theme,
				mode: "ace/mode/" + language,
				tabSize: tabSize,
				useSoftTabs: !indentWithTabs,
				showLineNumbers: showLineNumbers,
				showGutter: showLineNumbers,
				minLines: minLines,
				maxLines: maxLines,
				autoScrollEditorIntoView: true,
				showPrintMargin: false,
			});

			// Set value in and from the cloned field
			editor.getSession().setValue(clonedE.value);
			editor.getSession().on("change", function () {
				clonedE.value = editor.getSession().getValue();
			});

			// Set autosize
			let h = editor.getSession().getScreenLength() * (editor.renderer.lineHeight + editor.renderer.scrollBar.getWidth());
			h = Math.min(Math.max(h, 120), 200);
			editor.container.style.height = `${h}px`;
			editor.resize();
			editor.on("change", (arg, editor) => {
				let h = editor.getSession().getScreenLength() * (editor.renderer.lineHeight + editor.renderer.scrollBar.getWidth());
				h = Math.min(Math.max(h, 120), 200);
				editor.container.style.height = `${h}px`;
				editor.resize();
			});
		});
	};
})();
