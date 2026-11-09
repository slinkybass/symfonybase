/**
 * Ace codeeditor field
 *
 * Autor: slinkybass
 * Version: 3.3
 *
 * Contract: lib/ace.js.
 */

import { definePlugin } from "./lib/plugin.js";
import { ATTR, bindThemeSync, createEditor, editorDefaults, editorOptsFromElement } from "./lib/ace.js";

definePlugin("formTypeCodeEditor", {
    attr: ATTR.PLUGIN,
    datasetKey: "codeeditorInitialized",
    onReady() {
        bindThemeSync();
    },
    setup(el) {
        createEditor(el, editorOptsFromElement(el, editorDefaults()));
    },
});
