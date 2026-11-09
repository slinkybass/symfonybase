/**
 * IMask field
 *
 * Autor: slinkybass
 * Version: 4.0
 *
 * Contract: lib/imask.js.
 */

import { definePlugin } from "./lib/plugin.js";
import { ATTR, createMask, maskOptsFromElement } from "./lib/imask.js";

definePlugin("formTypeMask", {
    attr: ATTR.PLUGIN,
    datasetKey: "maskInitialized",
    setup(el) {
        createMask(el, maskOptsFromElement(el));
    },
});
