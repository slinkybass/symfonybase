/**
 * Signature pad field
 *
 * Autor: slinkybass
 * Version: 3.3
 *
 * Contract: lib/signaturepad.js.
 */

import { definePlugin } from "./lib/plugin.js";
import { ATTR, createPad, padOptsFromElement } from "./lib/signaturepad.js";

definePlugin("formTypeSignature", {
    attr: ATTR.PLUGIN,
    datasetKey: "signatureInitialized",
    setup(el) {
        createPad(el, padOptsFromElement(el));
    },
});
