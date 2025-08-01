/**
 * Flatpickr datetime field
 *
 * Autor: slinkybass
 * Version: 3.0
 */

import flatpickr from "flatpickr";
import "flatpickr/dist/l10n/index.js";
import "flatpickr/dist/flatpickr.min.css";

(function () {
	document.addEventListener("DOMContentLoaded", () => {
		formTypeDatetime();
	});

	document.addEventListener("ea.collection.item-added", () => {
		formTypeDatetime();
	});

	window.formTypeDatetime = function formTypeDatetime(selector = '[data-datetime-field="true"]') {
		document.querySelectorAll(selector).forEach((e) => {
			const max = e.hasAttribute("max") ? e.getAttribute("max") : null;
			const min = e.hasAttribute("min") ? e.getAttribute("min") : null;
			const inline = e.hasAttribute("data-date-inline") ? e.getAttribute("data-date-inline") !== "false" : false;
			const mode = e.hasAttribute("data-date-mode") ? e.getAttribute("data-date-mode") : "single";
			const dateFormat = e.hasAttribute("data-date-format") ? e.getAttribute("data-date-format") : "YYYY-MM-DDTHH:mm";
			const altFormat = e.hasAttribute("data-date-alt-format") ? e.getAttribute("data-date-alt-format") : moment.localeData().longDateFormat("L") + " " + moment.localeData().longDateFormat("LT");
			const minuteIncrement = e.hasAttribute("data-date-minute-increment") ? e.getAttribute("data-date-minute-increment") : 1;
			const enabledDates = e.hasAttribute("data-date-enabled") ? e.getAttribute("data-date-enabled") : null;
			const disabledDates = e.hasAttribute("data-date-disabled") ? e.getAttribute("data-date-disabled") : null;

			const flatPickrOtps = {
				locale: moment.locale(),
				inline: inline,
				mode: mode,
				dateFormat: dateFormat,
				altInput: true,
				altInputClass: inline ? "d-none" : "",
				altFormat: altFormat,
				allowInput: true,
				disableMobile: true,
				enableTime: true,
				time_24hr: true,
				minuteIncrement: minuteIncrement,
				locale: {
					rangeSeparator: ', '
				},
				parseDate: (datestr, format) => {
					return moment(datestr, format, true).toDate();
				},
				formatDate: (date, format, locale) => {
					return moment(date).format(format);
				},
			};

			if (max) {
				flatPickrOtps.maxDate = new Date(max);
			}
			if (min) {
				flatPickrOtps.minDate = new Date(min);
			}

			if (enabledDates) {
				flatPickrOtps.enable = [
					function (date) {
						const dates = enabledDates.split(",").map(function (dt) {
							const dateDt = new Date(dt);
							return new Date(dateDt.getTime() - dateDt.getTimezoneOffset() * 60 * 1000).toISOString().split("T")[0];
						});
						const iDate = new Date(date.getTime() - date.getTimezoneOffset() * 60 * 1000).toISOString().split("T")[0];
						return dates.find((dt) => dt === iDate) !== undefined;
					},
				];
				flatPickrOtps.onOpen = function (selectedDates, dateStr, instance) {
					const enabledDates = enabledDates
						.split(",")
						.map(function (dt) {
							const dateDt = new Date(dt);
							return new Date(dateDt.getTime() - dateDt.getTimezoneOffset() * 60 * 1000);
						})
						.sort((a, b) => a - b);
					instance.currentMonth = dates[0].getMonth();
					instance.currentYear = dates[0].getFullYear();
					instance.redraw();
				};
			} else if (disabledDates) {
				flatPickrOtps.disable = [
					function (date) {
						const dates = disabledDates.split(",").map(function (dt) {
							const dateDt = new Date(dt);
							return new Date(dateDt.getTime() - dateDt.getTimezoneOffset() * 60 * 1000).toISOString().split("T")[0];
						});
						const iDate = new Date(date.getTime() - date.getTimezoneOffset() * 60 * 1000).toISOString().split("T")[0];
						return dates.find((dt) => dt === iDate) !== undefined;
					},
				];
			}

			flatpickr(e, flatPickrOtps);
		});
	};
})();
