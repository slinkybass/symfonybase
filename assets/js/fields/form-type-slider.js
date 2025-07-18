/**
 * noUiSlider field
 *
 * Autor: slinkybass
 * Version: 3.0
 */

import noUiSlider from "nouislider";

(function () {
	document.addEventListener("DOMContentLoaded", () => {
		formTypeSlider();
	});

	document.addEventListener("ea.collection.item-added", () => {
		formTypeSlider();
	});

	window.formTypeSlider = function formTypeSlider(selector = '[data-slider-field="true"]') {
		document.querySelectorAll(selector).forEach((e) => {
			const max = e.hasAttribute("max") ? parseFloat(e.getAttribute("max")) : 100;
			const min = e.hasAttribute("min") ? parseFloat(e.getAttribute("min")) : 0;
			const start = e.value ? parseFloat(e.value.replace(",", ".")) : min;
			const step = e.hasAttribute("step") ? parseFloat(e.getAttribute("step").replace(",", ".")) || 1 : 1;
			const showInput = e.hasAttribute("data-slider-show-input") ? e.getAttribute("data-slider-show-input") !== "false" : false;
			const tooltips = e.hasAttribute("data-slider-tooltips") ? e.getAttribute("data-slider-tooltips") !== "false" : true;
			const connect = e.hasAttribute("data-slider-connect") ? e.getAttribute("data-slider-connect") : "lower";

			if (!showInput) {
				e.classList.add("d-none");
			}

			const slider = document.createElement("div");
			slider.classList.add("slider");
			e.parentNode.insertBefore(slider, e.nextSibling);

			noUiSlider.create(slider, {
				tooltips: tooltips,
				connect: connect,
				step: step,
				start: [start],
				format: {
					to: function (value) {
						return parseFloat(parseFloat(value).toFixed(2));
					},
					from: function (value) {
						return parseFloat(parseFloat(value).toFixed(2));
					},
				},
				range: {
					max: max,
					min: min,
				},
			});

			slider.noUiSlider.on("update", function (value) {
				e.value = value;
			});

			e.addEventListener("change", function () {
				slider.noUiSlider.set(this.value.replace(",", "."));
			});
			e.addEventListener("input", function () {
				slider.noUiSlider.set(this.value.replace(",", "."));
			});
		});
	};
})();
