/**
 * Ace helpers for the codeeditor field plugin.
 *
 * Autor: slinkybass
 * Version: 1.0
 *
 * HTML contract. Bool: omitted → default; "false"|"0" → false; else true.
 * Attr present → overrides default.
 *
 * Mapped: theme, dark-theme, language (mode), tab-size, indent-with-tabs,
 * show-line-numbers, show-gutter, min-lines, max-lines, font-size, font-family,
 * wrap, show-print-margin, print-margin-column, highlight-active-line,
 * highlight-selected-word, show-invisibles, display-indent-guides,
 * show-fold-widgets, use-worker.
 * Placeholder / readonly: native HTML `placeholder` / `readonly`
 * (FieldTrait::setPlaceholder / isReadonly) → Ace options.
 *
 * App infra (not data-*): theme sync via data-bs-theme — default chrome/twilight;
 * with theme + dark-theme, syncs that pair; theme alone stays fixed.
 * Ace CDN basePath for modes/themes/workers; license via ace-builds importmap.
 *
 * Not mapped: Ace extensions (autocomplete, snippets, emmet, keybinding);
 * annotations; custom commands; keyboardHandler; session/document APIs.
 */

import ace from "ace-builds/src-min-noconflict/ace";
import { boolAttr, intAttr, strAttr } from "./plugin.js";

const ACE_VERSION = "1.44.0";
const CDN = `https://cdn.jsdelivr.net/npm/ace-builds@${ACE_VERSION}/src-min-noconflict`;
ace.config.set("basePath", CDN);
ace.config.set("modePath", CDN);
ace.config.set("themePath", CDN);
ace.config.set("workerPath", CDN);

export const ATTR = {
    PLUGIN: "data-codeeditor-field",
    THEME: "data-codeeditor-theme",
    DARK_THEME: "data-codeeditor-dark-theme",
    LANGUAGE: "data-codeeditor-language",
    TAB_SIZE: "data-codeeditor-tab-size",
    INDENT_WITH_TABS: "data-codeeditor-indent-with-tabs",
    SHOW_LINE_NUMBERS: "data-codeeditor-show-line-numbers",
    SHOW_GUTTER: "data-codeeditor-show-gutter",
    MIN_LINES: "data-codeeditor-min-lines",
    MAX_LINES: "data-codeeditor-max-lines",
    FONT_SIZE: "data-codeeditor-font-size",
    FONT_FAMILY: "data-codeeditor-font-family",
    WRAP: "data-codeeditor-wrap",
    SHOW_PRINT_MARGIN: "data-codeeditor-show-print-margin",
    PRINT_MARGIN_COLUMN: "data-codeeditor-print-margin-column",
    HIGHLIGHT_ACTIVE_LINE: "data-codeeditor-highlight-active-line",
    HIGHLIGHT_SELECTED_WORD: "data-codeeditor-highlight-selected-word",
    SHOW_INVISIBLES: "data-codeeditor-show-invisibles",
    DISPLAY_INDENT_GUIDES: "data-codeeditor-display-indent-guides",
    SHOW_FOLD_WIDGETS: "data-codeeditor-show-fold-widgets",
    USE_WORKER: "data-codeeditor-use-worker",
    PLACEHOLDER: "placeholder",
    READONLY: "readonly",
};

export const THEME_LIGHT = "chrome";
export const THEME_DARK = "twilight";

export const DEFAULT_LANGUAGE = "javascript";
export const DEFAULT_TAB_SIZE = 4;
export const DEFAULT_MIN_LINES = 5;
export const DEFAULT_MAX_LINES = 20;
export const DEFAULT_PRINT_MARGIN_COLUMN = 80;

/** @type {{ editor: import("ace-builds").Ace.Editor, light: string, dark: string }[]} */
const themeBoundEditors = [];

let themeBound = false;

/** App defaults (preserve prior form-type-codeeditor behaviour). */
export function editorDefaults() {
    return {
        theme: null,
        darkTheme: null,
        language: DEFAULT_LANGUAGE,
        tabSize: DEFAULT_TAB_SIZE,
        indentWithTabs: true,
        showLineNumbers: true,
        showGutter: true,
        minLines: DEFAULT_MIN_LINES,
        maxLines: DEFAULT_MAX_LINES,
        fontSize: null,
        fontFamily: null,
        wrap: false,
        showPrintMargin: false,
        printMarginColumn: DEFAULT_PRINT_MARGIN_COLUMN,
        highlightActiveLine: true,
        highlightSelectedWord: true,
        showInvisibles: false,
        displayIndentGuides: true,
        showFoldWidgets: true,
        useWorker: true,
        placeholder: null,
        readOnly: false,
        autoScrollEditorIntoView: true,
    };
}

/**
 * @param {Element} el
 * @param {Record<string, unknown>} [defaults]
 */
export function editorOptsFromElement(el, defaults = editorDefaults()) {
    const opts = { ...defaults };

    if (el.hasAttribute(ATTR.THEME)) {
        opts.theme = strAttr(el, ATTR.THEME, null);
    }
    if (el.hasAttribute(ATTR.DARK_THEME)) {
        opts.darkTheme = strAttr(el, ATTR.DARK_THEME, null);
    }
    if (el.hasAttribute(ATTR.LANGUAGE)) {
        opts.language = strAttr(el, ATTR.LANGUAGE, DEFAULT_LANGUAGE);
    }
    if (el.hasAttribute(ATTR.TAB_SIZE)) {
        opts.tabSize = intAttr(el, ATTR.TAB_SIZE, DEFAULT_TAB_SIZE, 1);
    }
    if (el.hasAttribute(ATTR.INDENT_WITH_TABS)) {
        opts.indentWithTabs = boolAttr(el, ATTR.INDENT_WITH_TABS, true);
    }
    if (el.hasAttribute(ATTR.SHOW_LINE_NUMBERS)) {
        opts.showLineNumbers = boolAttr(el, ATTR.SHOW_LINE_NUMBERS, true);
    }
    if (el.hasAttribute(ATTR.SHOW_GUTTER)) {
        opts.showGutter = boolAttr(el, ATTR.SHOW_GUTTER, true);
    }
    if (el.hasAttribute(ATTR.MIN_LINES)) {
        opts.minLines = intAttr(el, ATTR.MIN_LINES, DEFAULT_MIN_LINES, 1);
    }
    if (el.hasAttribute(ATTR.MAX_LINES)) {
        opts.maxLines = intAttr(el, ATTR.MAX_LINES, DEFAULT_MAX_LINES, 1);
    }
    if (el.hasAttribute(ATTR.FONT_SIZE)) {
        opts.fontSize = intAttr(el, ATTR.FONT_SIZE, 14, 1);
    }
    if (el.hasAttribute(ATTR.FONT_FAMILY)) {
        opts.fontFamily = strAttr(el, ATTR.FONT_FAMILY, null);
    }
    if (el.hasAttribute(ATTR.WRAP)) {
        opts.wrap = boolAttr(el, ATTR.WRAP, false);
    }
    if (el.hasAttribute(ATTR.SHOW_PRINT_MARGIN)) {
        opts.showPrintMargin = boolAttr(el, ATTR.SHOW_PRINT_MARGIN, false);
    }
    if (el.hasAttribute(ATTR.PRINT_MARGIN_COLUMN)) {
        opts.printMarginColumn = intAttr(el, ATTR.PRINT_MARGIN_COLUMN, DEFAULT_PRINT_MARGIN_COLUMN, 1);
    }
    if (el.hasAttribute(ATTR.HIGHLIGHT_ACTIVE_LINE)) {
        opts.highlightActiveLine = boolAttr(el, ATTR.HIGHLIGHT_ACTIVE_LINE, true);
    }
    if (el.hasAttribute(ATTR.HIGHLIGHT_SELECTED_WORD)) {
        opts.highlightSelectedWord = boolAttr(el, ATTR.HIGHLIGHT_SELECTED_WORD, true);
    }
    if (el.hasAttribute(ATTR.SHOW_INVISIBLES)) {
        opts.showInvisibles = boolAttr(el, ATTR.SHOW_INVISIBLES, false);
    }
    if (el.hasAttribute(ATTR.DISPLAY_INDENT_GUIDES)) {
        opts.displayIndentGuides = boolAttr(el, ATTR.DISPLAY_INDENT_GUIDES, true);
    }
    if (el.hasAttribute(ATTR.SHOW_FOLD_WIDGETS)) {
        opts.showFoldWidgets = boolAttr(el, ATTR.SHOW_FOLD_WIDGETS, true);
    }
    if (el.hasAttribute(ATTR.USE_WORKER)) {
        opts.useWorker = boolAttr(el, ATTR.USE_WORKER, true);
    }

    const placeholder = strAttr(el, ATTR.PLACEHOLDER, null);
    if (placeholder !== null && placeholder !== "") {
        opts.placeholder = placeholder;
    }
    if (el.hasAttribute(ATTR.READONLY)) {
        opts.readOnly = boolAttr(el, ATTR.READONLY, false);
    }

    const minLines = Number(opts.minLines) || DEFAULT_MIN_LINES;
    const maxLines = Number(opts.maxLines) || DEFAULT_MAX_LINES;
    opts.minLines = minLines;
    opts.maxLines = Math.max(maxLines, minLines);

    return opts;
}

/**
 * Mount Ace next to the textarea and keep values in sync.
 *
 * @param {HTMLTextAreaElement} el
 * @param {Record<string, unknown>} [opts]
 */
export function createEditor(el, opts = editorOptsFromElement(el)) {
    const parent = el.parentNode;
    if (!parent) {
        return null;
    }

    const customTheme = typeof opts.theme === "string" && opts.theme !== "" ? opts.theme : null;
    const customDarkTheme = typeof opts.darkTheme === "string" && opts.darkTheme !== "" ? opts.darkTheme : null;
    const lightTheme = customTheme || THEME_LIGHT;
    const darkTheme = customDarkTheme || (customTheme ? customTheme : THEME_DARK);
    const followsAppTheme = !customTheme || Boolean(customDarkTheme);
    const theme = isDarkTheme() ? darkTheme : lightTheme;

    el.classList.add("d-none");
    // Drop EasyAdmin CodeMirror hook; we own the editor via data-codeeditor-field.
    el.removeAttribute("data-ea-code-editor-field");

    const editorEl = document.createElement("div");
    editorEl.className = "ace-editor";
    editorEl.style.width = "100%";
    parent.insertBefore(editorEl, el.nextSibling);

    /** @type {Record<string, unknown>} */
    const aceOpts = {
        theme: `ace/theme/${theme}`,
        mode: `ace/mode/${opts.language || DEFAULT_LANGUAGE}`,
        tabSize: Number(opts.tabSize) || DEFAULT_TAB_SIZE,
        useSoftTabs: !opts.indentWithTabs,
        showLineNumbers: Boolean(opts.showLineNumbers),
        showGutter: opts.showGutter !== false,
        minLines: opts.minLines,
        maxLines: opts.maxLines,
        autoScrollEditorIntoView: opts.autoScrollEditorIntoView !== false,
        showPrintMargin: Boolean(opts.showPrintMargin),
        printMarginColumn: Number(opts.printMarginColumn) || DEFAULT_PRINT_MARGIN_COLUMN,
        highlightActiveLine: opts.highlightActiveLine !== false,
        highlightSelectedWord: opts.highlightSelectedWord !== false,
        showInvisibles: Boolean(opts.showInvisibles),
        displayIndentGuides: opts.displayIndentGuides !== false,
        showFoldWidgets: opts.showFoldWidgets !== false,
        useWorker: opts.useWorker !== false,
        wrap: Boolean(opts.wrap),
        readOnly: Boolean(opts.readOnly),
    };
    if (opts.fontSize) {
        aceOpts.fontSize = Number(opts.fontSize);
    }
    if (opts.fontFamily) {
        aceOpts.fontFamily = String(opts.fontFamily);
    }
    if (opts.placeholder) {
        aceOpts.placeholder = String(opts.placeholder);
    }

    const editor = ace.edit(editorEl, aceOpts);

    if (followsAppTheme) {
        themeBoundEditors.push({ editor, light: lightTheme, dark: darkTheme });
    }

    editor.getSession().setValue(el.value);
    editor.getSession().on("change", () => {
        el.value = editor.getSession().getValue();
    });

    return editor;
}

/** Call once from the entrypoint `onReady`. */
export function bindThemeSync() {
    if (themeBound) {
        return;
    }
    themeBound = true;
    syncAceThemes();
    new MutationObserver(syncAceThemes).observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["data-bs-theme"],
    });
}

function isDarkTheme() {
    return document.documentElement.getAttribute("data-bs-theme") === "dark";
}

function syncAceThemes() {
    const dark = isDarkTheme();
    themeBoundEditors.forEach(({ editor, light, dark: darkName }) => {
        editor.setTheme(`ace/theme/${dark ? darkName : light}`);
    });
}
