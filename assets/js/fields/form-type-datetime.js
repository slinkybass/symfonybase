/**
 * Flatpickr datetime field
 *
 * Autor: slinkybass
 * Version: 3.3
 *
 * Contract: lib/flatpickr.js. Posted value: data-date-format (default YYYY-MM-DDTHH:mm[:ss]).
 */

import { definePlugin } from "./lib/plugin.js";
import { ATTR, createPicker, datetimePickerDefaults, localizeFlatpickr, pickerOptsFromElement } from "./lib/flatpickr.js";

definePlugin("formTypeDatetime", {
    attr: ATTR.DATETIME_PLUGIN,
    datasetKey: "datetimeInitialized",
    onReady: localizeFlatpickr,
    setup(el) {
        createPicker(el, pickerOptsFromElement(el, datetimePickerDefaults(el)));
    },
});
