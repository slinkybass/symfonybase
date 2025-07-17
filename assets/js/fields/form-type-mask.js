/**
 * Mask field
 *
 * Autor: slinkybass
 * Version: 3.0
 */

import IMask from "imask";

(function () {
	document.addEventListener("DOMContentLoaded", () => {
		formTypeMask();
	});

	document.addEventListener("ea.collection.item-added", () => {
		formTypeMask();
	});

	window.formTypeMask = function formTypeMask(selector = '[data-mask-field="true"]') {
		document.querySelectorAll(selector).forEach((e) => {
			const isRegex = e.hasAttribute("data-mask-regex") ? e.getAttribute("data-mask-regex") !== "false" : false;
			const maskPattern = e.hasAttribute("data-mask-pattern") ? e.getAttribute("data-mask-pattern") : null;
			const mask = isRegex && maskPattern ? new RegExp(maskPattern) : maskPattern;
			const overwrite = e.hasAttribute("data-mask-overwrite") ? e.getAttribute("data-mask-overwrite") !== "false" : false;
			const placeholderChar = e.hasAttribute("data-mask-placeholder") ? e.getAttribute("data-mask-placeholder") : null;

			if (mask) {
				IMask(e, {
					mask: mask,
					overwrite: overwrite,
					lazy: placeholderChar ? false : true,
					placeholderChar: placeholderChar,
				});
			}
		});
	};
})();
