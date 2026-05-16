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
            if (e.dataset.maskInitialized !== undefined) {
                return;
            }

            const isRegex = e.hasAttribute("data-mask-regex") ? e.getAttribute("data-mask-regex") !== "false" : false;
            const maskPattern = e.hasAttribute("data-mask-pattern") ? e.getAttribute("data-mask-pattern") : null;
            let mask = null;
            if (maskPattern) {
                if (isRegex) {
                    try {
                        mask = new RegExp(maskPattern);
                    } catch {
                        mask = null;
                    }
                } else {
                    mask = maskPattern;
                }
            }
            const overwrite = e.hasAttribute("data-mask-overwrite") ? e.getAttribute("data-mask-overwrite") !== "false" : false;
            const placeholderChar = e.hasAttribute("data-mask-placeholder") ? e.getAttribute("data-mask-placeholder") : null;

            if (mask) {
                e.dataset.maskInitialized = "";
                IMask(e, {
                    mask,
                    overwrite,
                    lazy: !placeholderChar,
                    placeholderChar,
                });
            }
        });
    };
})();
