/**
 * Textarea autogrow field
 *
 * Autor: slinkybass
 * Version: 3.0
 */

(function () {
	document.addEventListener("DOMContentLoaded", () => {
		formTypeTextarea();
	});
	document.addEventListener("ea.collection.item-added", () => {
		formTypeTextarea();
	});

	window.formTypeTextarea = function formTypeTextarea(selector = '[data-textarea-field="true"]') {
		document.querySelectorAll(selector).forEach((e) => {
			e.addEventListener("input", function () {
				autogrow(this);
			});
			autogrow(e);
		});

		function autogrow(field) {
			const maxHeight = field.hasAttribute("data-textarea-max-height") ? field.getAttribute("data-textarea-max-height") : "200px";

			field.style.overflow = "hidden";
			field.style.resize = "none";
			field.style.boxSizing = "border-box";
			field.style.height = "auto";
			field.style.maxHeight = maxHeight;

			// this check is needed because the <textarea> element can be inside a
			// minimizable panel, causing its scrollHeight value to be 0
			if (field.scrollHeight > 0) {
				field.style.height = field.scrollHeight + "px";
			}

			if (parseInt(field.style.height.replace("px", "")) > parseInt(maxHeight.replace("px", ""))) {
				field.style.overflow = "auto";
			}
		}
	};
})();
