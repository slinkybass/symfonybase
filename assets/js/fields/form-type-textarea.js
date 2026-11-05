/**
 * Textarea field
 *
 * Autor: slinkybass
 * Version: 3.3
 *
 * Contract: data-textarea-field on the textarea; optional data-textarea-max-height
 * (default 200px). Autogrow sets resize:none. Twig `resizeable` only applies when
 * the plugin is off (`enablePlugin(false)`).
 */

import { definePlugin, strAttr } from "./lib/plugin.js";

const ATTR = {
    PLUGIN: "data-textarea-field",
    MAX_HEIGHT: "data-textarea-max-height",
};

definePlugin("formTypeTextarea", {
    attr: ATTR.PLUGIN,
    datasetKey: "textareaInitialized",
    setup(el) {
        el.addEventListener("input", () => autogrow(el));
        autogrow(el);
    },
});

/** @param {HTMLElement} field */
function autogrow(field) {
    const maxHeight = strAttr(field, ATTR.MAX_HEIGHT, "200px");

    field.style.overflow = "hidden";
    field.style.resize = "none";
    field.style.boxSizing = "border-box";
    field.style.maxHeight = maxHeight;
    field.style.height = "auto";

    // Minimizable panels can report scrollHeight 0 while collapsed.
    if (field.scrollHeight > 0) {
        field.style.height = `${field.scrollHeight}px`;
    }

    if (field.scrollHeight > field.clientHeight + 1) {
        field.style.overflow = "auto";
    }
}
