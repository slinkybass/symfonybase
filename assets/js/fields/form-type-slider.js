/**
 * noUiSlider field
 *
 * Autor: slinkybass
 * Version: 3.3
 *
 * Contract: lib/nouislider.js.
 */

import { definePlugin } from "./lib/plugin.js";
import { ATTR, createSlider, sliderDefaults, sliderOptsFromElement } from "./lib/nouislider.js";

definePlugin("formTypeSlider", {
    attr: ATTR.PLUGIN,
    datasetKey: "sliderInitialized",
    setup(el) {
        createSlider(el, sliderOptsFromElement(el, sliderDefaults()));
    },
});
