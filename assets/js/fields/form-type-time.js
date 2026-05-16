/**
 * Flatpickr time field
 *
 * Autor: slinkybass
 * Version: 3.0
 */

import flatpickr from "flatpickr";
import "flatpickr/dist/l10n/index.js";
import "flatpickr/dist/flatpickr.min.css";

(function () {
    document.addEventListener("DOMContentLoaded", () => {
        const loc = moment.locale();
        const flatpickrLocale = flatpickr.l10ns[loc];
        if (flatpickrLocale) {
            flatpickr.localize(flatpickrLocale);
        }

        formTypeTime();
    });

    document.addEventListener("ea.collection.item-added", () => {
        formTypeTime();
    });

    window.formTypeTime = function formTypeTime(selector = '[data-time-field="true"]') {
        document.querySelectorAll(selector).forEach((e) => {
            if (e.dataset.timeInitialized !== undefined) {
                return;
            }

            e.dataset.timeInitialized = "";

            const max = e.hasAttribute("max") ? e.getAttribute("max") : null;
            const min = e.hasAttribute("min") ? e.getAttribute("min") : null;
            const inline = e.hasAttribute("data-date-inline") ? e.getAttribute("data-date-inline") !== "false" : false;
            const enableSeconds = e.hasAttribute("data-enable-seconds") ? e.getAttribute("data-enable-seconds") !== "false" : false;
            const minuteIncrementRaw = e.hasAttribute("data-date-minute-increment") ? e.getAttribute("data-date-minute-increment") : "1";
            const minuteIncrementNum = Number.parseInt(String(minuteIncrementRaw), 10);
            const minuteIncrement = Number.isFinite(minuteIncrementNum) && minuteIncrementNum > 0 ? minuteIncrementNum : 1;
            const allowInput = e.hasAttribute("readonly") ? e.getAttribute("readonly") === "false" : true;

            const flatPickrOtps = {
                inline,
                altInputClass: inline ? "d-none" : "",
                altInput: inline,
                allowInput,
                disableMobile: true,
                enableTime: true,
                time_24hr: true,
                enableSeconds,
                minuteIncrement,
                noCalendar: true,
                onOpen: (selectedDates, dateStr, instance) => {
                    if (!instance.element.value) {
                        const h = instance.config.defaultHour;
                        const m = instance.config.defaultMinute;
                        const t = `${(`0${h}`).slice(-2)}:${(`0${m}`).slice(-2)}`;
                        instance.setDate(t, true);
                    }
                },
            };

            if (max) {
                flatPickrOtps.maxTime = max;
                if (!min) {
                    const maxParts = max.split(":");
                    if (maxParts.length >= 2) {
                        flatPickrOtps.defaultHour = maxParts[0];
                        flatPickrOtps.defaultMinute = maxParts[1];
                    }
                }
            }
            if (min) {
                flatPickrOtps.minTime = min;
                const minParts = min.split(":");
                if (minParts.length >= 2) {
                    flatPickrOtps.defaultHour = minParts[0];
                    flatPickrOtps.defaultMinute = minParts[1];
                }
            }

            flatpickr(e, flatPickrOtps);
        });
    };
})();
