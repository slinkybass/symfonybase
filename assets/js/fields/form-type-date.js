/**
 * Flatpickr date field
 *
 * Autor: slinkybass
 * Version: 3.3
 *
 * Contract: lib/flatpickr.js. Posted value: data-date-format (default YYYY-MM-DD).
 */

import { definePlugin } from "./lib/plugin.js";
import { ATTR, createPicker, datePickerDefaults, localizeFlatpickr, pickerOptsFromElement } from "./lib/flatpickr.js";

definePlugin("formTypeDate", {
    attr: ATTR.DATE_PLUGIN,
    datasetKey: "dateInitialized",
    onReady: localizeFlatpickr,
    setup(el) {
        createPicker(el, pickerOptsFromElement(el, datePickerDefaults()));
    },
});
