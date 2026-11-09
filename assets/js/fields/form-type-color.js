/**
 * Spectrum color field
 *
 * Autor: slinkybass
 * Version: 4.0
 *
 * Contract: lib/spectrum.js.
 */

import { definePlugin } from "./lib/plugin.js";
import { ATTR, colorPickerDefaults, createPicker, pickerOptsFromElement } from "./lib/spectrum.js";

definePlugin("formTypeColor", {
    attr: ATTR.PLUGIN,
    datasetKey: "colorInitialized",
    setup(el) {
        createPicker(el, pickerOptsFromElement(el, colorPickerDefaults()));
    },
});
