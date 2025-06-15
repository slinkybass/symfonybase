/**
 * TinyMCE field
 *
 * Autor: slinkybass
 * Version: 3.0
 */

import tinymce from "tinymce";
import "tinymce/models/dom/model";
import "tinymce/themes/silver";
import "tinymce/icons/default";
import "tinymce/skins/ui/oxide/skin.min.css";

import "tinymce-i18n/langs/es.js";

import "tinymce/plugins/accordion";
import "tinymce/plugins/advlist";
import "tinymce/plugins/anchor";
import "tinymce/plugins/autolink";
import "tinymce/plugins/autoresize";
import "tinymce/plugins/autosave";
import "tinymce/plugins/charmap";
import "tinymce/plugins/code";
import "tinymce/plugins/codesample";
import "tinymce/plugins/directionality";
import "tinymce/plugins/emoticons";
import "tinymce/plugins/emoticons/js/emojis";
import "tinymce/plugins/fullscreen";
import "tinymce/plugins/image";
import "tinymce/plugins/importcss";
import "tinymce/plugins/insertdatetime";
import "tinymce/plugins/link";
import "tinymce/plugins/lists";
import "tinymce/plugins/media";
import "tinymce/plugins/nonbreaking";
import "tinymce/plugins/pagebreak";
import "tinymce/plugins/preview";
import "tinymce/plugins/quickbars";
import "tinymce/plugins/save";
import "tinymce/plugins/searchreplace";
import "tinymce/plugins/table";
import "tinymce/plugins/visualblocks";
import "tinymce/plugins/visualchars";
import "tinymce/plugins/wordcount";

(function () {
	document.addEventListener("DOMContentLoaded", () => {
		formTypeTextEditor();
	});
	document.addEventListener("ea.collection.item-added", () => {
		formTypeTextEditor();
	});

	window.formTypeTextEditor = function formTypeTextEditor(selector = '[data-texteditor-field="true"]') {
		document.querySelectorAll(selector).forEach((e) => {
			const resize = e.hasAttribute("data-texteditor-resize") ? e.getAttribute("data-texteditor-resize") !== "false" : false;
			const spellcheck = e.hasAttribute("data-texteditor-spellcheck") ? e.getAttribute("data-texteditor-spellcheck") !== "false" : false;
			const defaultToolbar =
				"undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | link image media table insertfile | emoticons charmap | pagebreak anchor | codesample code | preview fullscreen | print";
			const toolbar = e.hasAttribute("data-texteditor-toolbar") ? e.getAttribute("data-texteditor-toolbar") : defaultToolbar;

			tinymce.init({
				plugins: [
					"accordion",
					"advlist",
					"anchor",
					"autolink",
					"autoresize",
					"autosave",
					"charmap",
					"code",
					"codesample",
					"directionality",
					"emoticons",
					"fullscreen",
					"image",
					"importcss",
					"insertdatetime",
					"link",
					"lists",
					"media",
					"nonbreaking",
					"pagebreak",
					"preview",
					"quickbars",
					"save",
					"searchreplace",
					"table",
					"visualblocks",
					"visualchars",
					"wordcount",
				],
				target: e,
				language: moment.locale(),
				resize: resize,
				browser_spellcheck: spellcheck,
				toolbar: toolbar,
				menubar: false,
				contextmenu: false,
				quickbars_selection_toolbar: false,
				quickbars_insert_toolbar: false,
				autoresize_bottom_margin: 0,
				skin: false,
				content_css: false,
				branding: false,
				convert_urls: false,
			});
		});
	};
})();
