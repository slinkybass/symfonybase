/**
 * Spectrum helpers for the color plugin.
 *
 * Autor: slinkybass
 * Version: 1.1
 *
 * HTML contract. Bool: omitted → default; "false"|"0" → false; else true.
 * Attr present → overrides default. showPalette is forced on when palette-only.
 * showInput defaults to true when type is flat and the attr is omitted.
 *
 * Mapped: type, color, preferred-format, show-input, show-initial, allow-empty,
 * show-alpha, show-buttons, clickout-fires-change, show-palette, palette-only,
 * hide-after-palette-select, toggle-palette-only, show-selection-palette,
 * local-storage-key, max-selection-size, palette, selection-palette,
 * container-class-name, replacer-class-name, theme, locale, cancel/choose/
 * toggle-palette-more/less/clear/no-color-selected text.
 *
 * Not mapped: beforeShow, move, change, show, hide (hooks), appendTo, offset,
 * disabled (native / FieldTrait), locale as object.
 */

import Spectrum from "spectrum-vanilla";
import "spectrum-vanilla/dist/spectrum.min.css";
import { boolAttr, intAttr, strAttr } from "./plugin.js";

export const ATTR = {
    PLUGIN: "data-color-field",
    TYPE: "data-color-type",
    COLOR: "data-color-color",
    PREFERRED_FORMAT: "data-color-preferred-format",
    SHOW_INPUT: "data-color-show-input",
    SHOW_INITIAL: "data-color-show-initial",
    ALLOW_EMPTY: "data-color-allow-empty",
    SHOW_ALPHA: "data-color-show-alpha",
    SHOW_BUTTONS: "data-color-show-buttons",
    CLICKOUT_FIRES_CHANGE: "data-color-clickout-fires-change",
    SHOW_PALETTE: "data-color-show-palette",
    PALETTE_ONLY: "data-color-palette-only",
    HIDE_AFTER_PALETTE_SELECT: "data-color-hide-after-palette-select",
    TOGGLE_PALETTE_ONLY: "data-color-toggle-palette-only",
    SHOW_SELECTION_PALETTE: "data-color-show-selection-palette",
    LOCAL_STORAGE_KEY: "data-color-local-storage-key",
    MAX_SELECTION_SIZE: "data-color-max-selection-size",
    PALETTE: "data-color-palette",
    SELECTION_PALETTE: "data-color-selection-palette",
    CONTAINER_CLASS_NAME: "data-color-container-class-name",
    REPLACER_CLASS_NAME: "data-color-replacer-class-name",
    THEME: "data-color-theme",
    LOCALE: "data-color-locale",
    CANCEL_TEXT: "data-color-cancel-text",
    CHOOSE_TEXT: "data-color-choose-text",
    TOGGLE_PALETTE_MORE_TEXT: "data-color-toggle-palette-more-text",
    TOGGLE_PALETTE_LESS_TEXT: "data-color-toggle-palette-less-text",
    CLEAR_TEXT: "data-color-clear-text",
    NO_COLOR_SELECTED_TEXT: "data-color-no-color-selected-text",
};

export const TYPE_FLAT = "flat";

const BOOL_MAP = [
    [ATTR.SHOW_INPUT, "showInput"],
    [ATTR.SHOW_INITIAL, "showInitial"],
    [ATTR.ALLOW_EMPTY, "allowEmpty"],
    [ATTR.SHOW_ALPHA, "showAlpha"],
    [ATTR.SHOW_BUTTONS, "showButtons"],
    [ATTR.CLICKOUT_FIRES_CHANGE, "clickoutFiresChange"],
    [ATTR.SHOW_PALETTE, "showPalette"],
    [ATTR.PALETTE_ONLY, "showPaletteOnly"],
    [ATTR.HIDE_AFTER_PALETTE_SELECT, "hideAfterPaletteSelect"],
    [ATTR.TOGGLE_PALETTE_ONLY, "togglePaletteOnly"],
    [ATTR.SHOW_SELECTION_PALETTE, "showSelectionPalette"],
];

const STR_MAP = [
    [ATTR.TYPE, "type"],
    [ATTR.COLOR, "color"],
    [ATTR.PREFERRED_FORMAT, "preferredFormat"],
    [ATTR.LOCAL_STORAGE_KEY, "localStorageKey"],
    [ATTR.CONTAINER_CLASS_NAME, "containerClassName"],
    [ATTR.REPLACER_CLASS_NAME, "replacerClassName"],
    [ATTR.THEME, "theme"],
    [ATTR.LOCALE, "locale"],
    [ATTR.CANCEL_TEXT, "cancelText"],
    [ATTR.CHOOSE_TEXT, "chooseText"],
    [ATTR.TOGGLE_PALETTE_MORE_TEXT, "togglePaletteMoreText"],
    [ATTR.TOGGLE_PALETTE_LESS_TEXT, "togglePaletteLessText"],
    [ATTR.CLEAR_TEXT, "clearText"],
    [ATTR.NO_COLOR_SELECTED_TEXT, "noColorSelectedText"],
];

const INT_MAP = [[ATTR.MAX_SELECTION_SIZE, "maxSelectionSize", 1]];

const JSON_MAP = [
    [ATTR.PALETTE, "palette"],
    [ATTR.SELECTION_PALETTE, "selectionPalette"],
];

function jsonAttr(el, name) {
    const raw = strAttr(el, name);
    if (raw === null || raw === "") {
        return undefined;
    }
    try {
        return JSON.parse(raw);
    } catch {
        return undefined;
    }
}

/** App defaults (preserve prior form-type-color behaviour over Spectrum vendor defaults). */
export function colorPickerDefaults() {
    return {
        type: "component",
        preferredFormat: "hex",
        showInput: false,
        showInitial: false,
        allowEmpty: true,
        showAlpha: false,
        showButtons: false,
        clickoutFiresChange: true,
        showPalette: false,
        showPaletteOnly: false,
        hideAfterPaletteSelect: true,
        togglePaletteOnly: false,
        showSelectionPalette: true,
    };
}

/**
 * @param {Element} el
 * @param {Record<string, unknown>} [defaults]
 */
export function pickerOptsFromElement(el, defaults = colorPickerDefaults()) {
    const opts = { ...defaults };

    for (const [attr, key] of BOOL_MAP) {
        if (el.hasAttribute(attr)) {
            opts[key] = boolAttr(el, attr, false);
        }
    }
    for (const [attr, key] of STR_MAP) {
        if (el.hasAttribute(attr)) {
            opts[key] = strAttr(el, attr);
        }
    }
    for (const [attr, key, min] of INT_MAP) {
        if (el.hasAttribute(attr)) {
            opts[key] = intAttr(el, attr, opts[key] ?? min, min);
        }
    }
    for (const [attr, key] of JSON_MAP) {
        if (el.hasAttribute(attr)) {
            const parsed = jsonAttr(el, attr);
            if (parsed !== undefined) {
                opts[key] = parsed;
            }
        }
    }

    if (!el.hasAttribute(ATTR.SHOW_INPUT) && opts.type === TYPE_FLAT) {
        opts.showInput = true;
    }
    if (opts.showPaletteOnly) {
        opts.showPalette = true;
    }

    return opts;
}

/** @param {Element} el @param {Record<string, unknown>} [opts] */
export function createPicker(el, opts = {}) {
    return Spectrum.create(el, opts);
}
