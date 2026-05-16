/**
 * noUiSlider field
 *
 * Autor: slinkybass
 * Version: 3.1
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

            const parent = e.parentNode;
            if (!parent) {
                return;
            }

            e.dataset.sliderInitialized = "";

            let max = e.hasAttribute("max") ? Number.parseFloat(String(e.getAttribute("max"))) : 100;
            let min = e.hasAttribute("min") ? Number.parseFloat(String(e.getAttribute("min"))) : 0;
            if (!Number.isFinite(min)) {
                min = 0;
            }
            if (!Number.isFinite(max)) {
                max = 100;
            }
            if (max < min) {
                const t = min;
                min = max;
                max = t;
            }
            let start = e.value ? Number.parseFloat(String(e.value).replace(",", ".")) : min;
            if (!Number.isFinite(start)) {
                start = min;
            }
            if (start < min) {
                start = min;
            }
            if (start > max) {
                start = max;
            }
            const stepAttr = e.hasAttribute("step") ? e.getAttribute("step") : null;
            const stepParsed = stepAttr ? Number.parseFloat(String(stepAttr).replace(",", ".")) : 1;
            const step = Number.isFinite(stepParsed) && stepParsed > 0 ? stepParsed : 1;
            const showInput = e.hasAttribute("data-slider-show-input") ? e.getAttribute("data-slider-show-input") !== "false" : false;
            const tooltips = e.hasAttribute("data-slider-tooltips") ? e.getAttribute("data-slider-tooltips") !== "false" : true;
            const connect = e.hasAttribute("data-slider-connect") ? e.getAttribute("data-slider-connect") : "lower";
            const pips = e.hasAttribute("data-slider-pips") ? e.getAttribute("data-slider-pips") !== "false" : false;

            if (!showInput) {
                e.classList.add("d-none");
            }

            const slider = document.createElement("div");
            slider.classList.add("slider");
            parent.insertBefore(slider, e.nextSibling);

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

            const updateSlider = () => {
                if (!slider.noUiSlider) {
                    return;
                }
                const raw = String(e.value ?? "").replace(",", ".");
                const n = Number.parseFloat(raw);
                if (Number.isFinite(n)) {
                    slider.noUiSlider.set(n);
                }
            };
            e.addEventListener("change", updateSlider);
            e.addEventListener("input", updateSlider);
        });
    };
})();
