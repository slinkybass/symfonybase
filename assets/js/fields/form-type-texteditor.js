/**
 * TinyMCE texteditor field
 *
 * Autor: slinkybass
 * Version: 4.0
 *
 * Contract: lib/tinymce.js.
 */

import { definePlugin } from "./lib/plugin.js";
import { ATTR, bindThemeSync, createEditor, editorDefaults, editorOptsFromElement } from "./lib/tinymce.js";

definePlugin("formTypeTextEditor", {
    attr: ATTR.PLUGIN,
    datasetKey: "texteditorInitialized",
    onReady() {
        bindThemeSync();
    },
    setup(el) {
        createEditor(el, editorOptsFromElement(el, editorDefaults()));
    },
});
