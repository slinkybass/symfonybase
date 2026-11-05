/**
 * Slug field
 *
 * Autor: slinkybass
 * Version: 3.3
 *
 * Contract: lib/slugify.js.
 */

import { definePlugin } from "./lib/plugin.js";
import { ATTR, applySlugifyExtend, createSlugger, slugOptsFromElement, slugifyDefaults } from "./lib/slugify.js";

definePlugin("formTypeSlug", {
    attr: ATTR.PLUGIN,
    datasetKey: "slugInitialized",
    onReady() {
        applySlugifyExtend();
    },
    setup(el) {
        try {
            createSlugger(el, slugOptsFromElement(el, slugifyDefaults()));
        } catch (err) {
            console.error(err);
        }
    },
});
