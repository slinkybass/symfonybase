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
			if (e.dataset.sliderInitialized !== undefined) {
				return;
			}

			e.dataset.sliderInitialized = "";

			const max = e.hasAttribute("max") ? parseFloat(e.getAttribute("max")) : 100;
			const min = e.hasAttribute("min") ? parseFloat(e.getAttribute("min")) : 0;
			const start = e.value ? parseFloat(e.value.replace(",", ".")) : min;
			const step = e.hasAttribute("step") ? parseFloat(e.getAttribute("step").replace(",", ".")) || 1 : 1;
			const showInput = e.hasAttribute("data-slider-show-input") ? e.getAttribute("data-slider-show-input") !== "false" : false;
			const tooltips = e.hasAttribute("data-slider-tooltips") ? e.getAttribute("data-slider-tooltips") !== "false" : true;
			const connect = e.hasAttribute("data-slider-connect") ? e.getAttribute("data-slider-connect") : "lower";
			const pips = e.hasAttribute("data-slider-pips") ? e.getAttribute("data-slider-pips") !== "false" : false;

			if (!showInput) {
				e.classList.add("d-none");
			}

			const slider = document.createElement("div");
			slider.classList.add("slider");
			e.parentNode.insertBefore(slider, e.nextSibling);

			const noUiSliderOtps = {
				tooltips,
				connect,
				step,
				start,
				format: {
					to: (value) => {
						return parseFloat(parseFloat(value).toFixed(2));
					},
					from: (value) => {
						return parseFloat(parseFloat(value).toFixed(2));
					},
				},
				range: {
					max,
					min,
				},
			};

			if (pips) {
				noUiSliderOtps.pips = {
					mode: "steps",
					desity: 100,
					format: {
						to: (value) => {
							return parseFloat(parseFloat(value).toFixed(2));
						},
						from: (value) => {
							return parseFloat(parseFloat(value).toFixed(2));
						},
					},
				};
			}

			noUiSlider.create(slider, noUiSliderOtps);

			slider.noUiSlider.on("update", (value) => {
				e.value = value;
			});

			const updateSlider = () => slider.noUiSlider.set(e.value.replace(",", "."));
			e.addEventListener("change", updateSlider);
			e.addEventListener("input", updateSlider);
		});
	};
})();
