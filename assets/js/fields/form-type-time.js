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
		formTypeTime();
	});

	document.addEventListener("ea.collection.item-added", () => {
		formTypeTime();
	});

	window.formTypeTime = function formTypeTime(selector = '[data-time-field="true"]') {
		document.querySelectorAll(selector).forEach((e) => {
			const max = e.hasAttribute("max") ? e.getAttribute("max") : null;
			const min = e.hasAttribute("min") ? e.getAttribute("min") : null;
			const inline = e.hasAttribute("data-date-inline") ? e.getAttribute("data-date-inline") !== "false" : false;
			const enableSeconds = e.hasAttribute("data-enable-seconds") ? e.getAttribute("data-enable-seconds") !== "false" : false;
			const minuteIncrement = e.hasAttribute("data-date-minute-increment") ? e.getAttribute("data-date-minute-increment") : 1;
			const allowInput = e.hasAttribute("readonly") ? e.getAttribute("readonly") == "false" : true;

			const flatPickrOtps = {
				locale: moment.locale(),
				inline: inline,
				altInputClass: inline ? "d-none" : "",
				altInput: inline,
				allowInput: allowInput,
				disableMobile: true,
				enableTime: true,
				time_24hr: true,
				enableSeconds: enableSeconds,
				minuteIncrement: minuteIncrement,
				noCalendar: true,
				onOpen: function (selectedDates, dateStr, instance) {
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
					flatPickrOtps.defaultHour = max.split(":")[0];
					flatPickrOtps.defaultMinute = max.split(":")[1];
				}
			}
			if (min) {
				flatPickrOtps.minTime = min;
				flatPickrOtps.defaultHour = min.split(":")[0];
				flatPickrOtps.defaultMinute = min.split(":")[1];
			}

			flatpickr(e, flatPickrOtps);
		});
	};
})();
