/**
 * Codeeditor field
 *
 * Autor: slinkybass
 * Version: 3.0
 */

import ace from "ace-builds/src-min-noconflict/ace";

const CDN = 'https://cdn.jsdelivr.net/npm/ace-builds@latest/src-min-noconflict';
ace.config.set('basePath', CDN);
ace.config.set('modePath', CDN);
ace.config.set('themePath', CDN);
ace.config.set('workerPath', CDN);

(function () {
	document.addEventListener("DOMContentLoaded", () => {
		formTypeCodeeditor();
	});
	document.addEventListener("ea.collection.item-added", () => {
		formTypeCodeeditor();
	});

	window.formTypeCodeeditor = function formTypeCodeeditor(selector = '[data-codeeditor-field="true"]') {
		document.querySelectorAll(selector).forEach((e) => {
			const theme = e.hasAttribute("data-theme") ? e.getAttribute("data-theme") : "chrome";
			const language = e.hasAttribute("data-language") ? e.getAttribute("data-language") : "javascript";
			const tabSize = e.hasAttribute("data-tab-size") ? e.getAttribute("data-tab-size") : 4;
			const indentWithTabs = e.hasAttribute("data-indent-with-tabs") ? e.getAttribute("data-indent-with-tabs") !== "false" : false;
			const showLineNumbers = e.hasAttribute("data-show-line-numbers") ? e.getAttribute("data-show-line-numbers") !== "false" : false;
			const minLines = e.hasAttribute("data-min-lines") ? e.getAttribute("data-min-lines") : null;
			const maxLines = e.hasAttribute("data-max-lines") ? e.getAttribute("data-max-lines") : null;

			// Clone and hide the original field
			const clonedE = e.cloneNode(true);
			clonedE.classList.add("d-none");
			e.parentNode.insertBefore(clonedE, e);

			const editor = ace.edit(e, {
				'theme': "ace/theme/" + theme,
				'mode': "ace/mode/" + language,
				'tabSize': tabSize,
				'useSoftTabs': !indentWithTabs,
				'showLineNumbers': showLineNumbers,
				'showGutter': showLineNumbers,
				'minLines': minLines,
				'maxLines': maxLines,
				'autoScrollEditorIntoView': true,
				'showPrintMargin': false
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
			editor.on('change', (arg, editor) => {
				let h = editor.getSession().getScreenLength() * (editor.renderer.lineHeight + editor.renderer.scrollBar.getWidth());
				h = Math.min(Math.max(h, 120), 200);
				editor.container.style.height = `${h}px`;
				editor.resize();
			});
		});
	};
})();
