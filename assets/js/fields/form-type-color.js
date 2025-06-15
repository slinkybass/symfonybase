/**
 * Spectrum field
 *
 * Autor: slinkybass
 * Version: 3.0
 */

import Spectrum from "spectrum-vanilla";
import "spectrum-vanilla/dist/spectrum.min.css";

(function () {
	document.addEventListener("DOMContentLoaded", () => {
		formTypeColor();
	});

	document.addEventListener("ea.collection.item-added", () => {
		formTypeColor();
	});

	window.formTypeColor = function formTypeColor(selector = '[data-color-field="true"]') {
		document.querySelectorAll(selector).forEach((e) => {
			const type = e.hasAttribute("data-color-type") ? e.getAttribute("data-color-type") : "component";
			const preferredFormat = e.hasAttribute("data-color-preferred-format") ? e.getAttribute("data-color-preferred-format") : "hex";
			const showPalette = e.hasAttribute("data-color-show-palette") ? e.getAttribute("data-color-show-palette") !== "false" : false;
			const showPaletteOnly = e.hasAttribute("data-color-palette-only") ? e.getAttribute("data-color-palette-only") !== "false" : false;
			const showAlpha = e.hasAttribute("data-color-show-alpha") ? e.getAttribute("data-color-show-alpha") !== "false" : false;
			const showInput = e.hasAttribute("data-color-type") ? e.getAttribute("data-color-type") == "flat" : false;
			const hideAfterPaletteSelect = e.hasAttribute("data-color-hide-after-palette-select") ? e.getAttribute("data-color-hide-after-palette-select") !== "false" : true;

			Spectrum.create(e, {
				type: type,
				preferredFormat: preferredFormat,
				showPalette: showPalette || showPaletteOnly,
				showPaletteOnly: showPaletteOnly,
				showAlpha: showAlpha,
				showInput: showInput,
				hideAfterPaletteSelect: hideAfterPaletteSelect,
				showButtons: false,
			});
		});
	};
})();
