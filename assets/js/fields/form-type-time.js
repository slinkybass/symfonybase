/**
 * Flatpickr time field
 *
 * Autor: slinkybass
 * Version: 3.3
 *
 * Contract: lib/flatpickr.js. Posted value: data-date-format (default HH:mm or HH:mm:ss).
 */

import { definePlugin } from "./lib/plugin.js";
import { ATTR, createPicker, localizeFlatpickr, pickerOptsFromElement, timePickerDefaults } from "./lib/flatpickr.js";

definePlugin("formTypeTime", {
    attr: ATTR.TIME_PLUGIN,
    datasetKey: "timeInitialized",
    onReady: localizeFlatpickr,
    setup(el) {
        createPicker(el, pickerOptsFromElement(el, timePickerDefaults(el)));
    },
});
