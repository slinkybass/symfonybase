/**
 * TinyMCE helpers for the texteditor field plugin.
 *
 * Autor: slinkybass
 * Version: 1.0
 *
 * HTML contract. Bool: omitted → default; "false"|"0" → false; else true.
 * Attr present → overrides default.
 *
 * Mapped: resize (bool|both), browser-spellcheck, toolbar, menubar, plugins,
 * height, min-height, max-height, language, readonly, branding,
 * elementpath, statusbar, toolbar-mode, toolbar-sticky, autoresize-bottom-margin,
 * convert-urls, paste-as-text, contextmenu, quickbars-selection-toolbar,
 * quickbars-insert-toolbar, content-style (appended after Tabler theme style).
 * Placeholder / readonly: native HTML `placeholder` / `readonly`
 * (FieldTrait::setPlaceholder / isReadonly) → TinyMCE options. Placeholder
 * content CSS is injected because `content_css` is false.
 *
 * Presets (`data-texteditor-preset`): named bundles applied after defaults and
 * before per-option attrs (explicit attrs win). `basic` ≈ safe public content
 * (no chrome; autoresize; min 120 / max 200; toolbar: undo/redo, b/i/u, lists,
 * link, emoticons, charmap, preview; paste_as_text + valid_elements).
 *
 * Height vs autoresize: TinyMCE ignores `height` while `autoresize` is loaded.
 * `data-texteditor-height` drops `autoresize` from the default plugin list (fixed
 * height). If `data-texteditor-plugins` still includes autoresize, `height` is
 * applied as `min_height` when min-height was not set. Use min/max-height for
 * autoresize bounds.
 *
 * App infra (not data-*): oxide/oxide-dark skin toggle via data-bs-theme;
 * content body colors from Tabler CSS vars; license_key gpl; skin/content_css false.
 *
 * Not mapped: setup/init callbacks; file_picker_callback; custom formats/style_formats;
 * images_upload_*; paste_preprocess; dynamic plugin loading beyond the bundled set.
 */

import tinymce from "tinymce";
import "tinymce/models/dom/model";
import "tinymce/themes/silver";
import "tinymce/icons/default";
import "tinymce/skins/ui/oxide/skin.min.css";
import "tinymce/skins/ui/oxide-dark/skin.min.css";

import "tinymce-i18n/langs/es.js";
import esExtra from "../i18n/tinymce.es.js";

import "tinymce/plugins/accordion";
import "tinymce/plugins/advlist";
import "tinymce/plugins/anchor";
import "tinymce/plugins/autolink";
import "tinymce/plugins/autoresize";
import "tinymce/plugins/autosave";
import "tinymce/plugins/charmap";
import "tinymce/plugins/code";
import "tinymce/plugins/codesample";
import "tinymce/plugins/directionality";
import "tinymce/plugins/emoticons";
import "tinymce/plugins/emoticons/js/emojis";
import "tinymce/plugins/fullscreen";
import "tinymce/plugins/image";
import "tinymce/plugins/importcss";
import "tinymce/plugins/insertdatetime";
import "tinymce/plugins/link";
import "tinymce/plugins/lists";
import "tinymce/plugins/media";
import "tinymce/plugins/nonbreaking";
import "tinymce/plugins/pagebreak";
import "tinymce/plugins/preview";
import "tinymce/plugins/quickbars";
import "tinymce/plugins/save";
import "tinymce/plugins/searchreplace";
import "tinymce/plugins/table";
import "tinymce/plugins/visualblocks";
import "tinymce/plugins/visualchars";
import "tinymce/plugins/wordcount";

import { appLocale, boolAttr, intAttr, strAttr } from "./plugin.js";

tinymce.addI18n("es", esExtra);

export const ATTR = {
    PLUGIN: "data-texteditor-field",
    PRESET: "data-texteditor-preset",
    RESIZE: "data-texteditor-resize",
    SPELLCHECK: "data-texteditor-spellcheck",
    TOOLBAR: "data-texteditor-toolbar",
    MENUBAR: "data-texteditor-menubar",
    PLUGINS: "data-texteditor-plugins",
    HEIGHT: "data-texteditor-height",
    MIN_HEIGHT: "data-texteditor-min-height",
    MAX_HEIGHT: "data-texteditor-max-height",
    LANGUAGE: "data-texteditor-language",
    PLACEHOLDER: "placeholder",
    READONLY: "readonly",
    BRANDING: "data-texteditor-branding",
    ELEMENTPATH: "data-texteditor-elementpath",
    STATUSBAR: "data-texteditor-statusbar",
    TOOLBAR_MODE: "data-texteditor-toolbar-mode",
    TOOLBAR_STICKY: "data-texteditor-toolbar-sticky",
    AUTORESIZE_BOTTOM_MARGIN: "data-texteditor-autoresize-bottom-margin",
    CONVERT_URLS: "data-texteditor-convert-urls",
    PASTE_AS_TEXT: "data-texteditor-paste-as-text",
    CONTEXTMENU: "data-texteditor-contextmenu",
    QUICKBARS_SELECTION_TOOLBAR: "data-texteditor-quickbars-selection-toolbar",
    QUICKBARS_INSERT_TOOLBAR: "data-texteditor-quickbars-insert-toolbar",
    CONTENT_STYLE: "data-texteditor-content-style",
};

export const PRESET_BASIC = "basic";

/** Named option bundles. Applied before per-attr overrides. */
export const PRESETS = {
    // Safe for public HTML dumps: semantic marks/lists/links only (no fonts/colors/blocks).
    [PRESET_BASIC]: {
        plugins: ["autolink", "autoresize", "charmap", "emoticons", "link", "lists", "preview"],
        toolbar: "undo redo | bold italic underline | bullist numlist | link emoticons charmap | preview",
        menubar: false,
        statusbar: false,
        elementpath: false,
        contextmenu: false,
        quickbars_selection_toolbar: false,
        quickbars_insert_toolbar: false,
        browser_spellcheck: true,
        resize: false,
        min_height: 120,
        max_height: 200,
        paste_as_text: true,
        valid_elements: "p,br,strong/b,em/i,u,a[href|target|rel],ul,ol,li",
    },
};

export const DEFAULT_PLUGINS = ["accordion", "advlist", "anchor", "autolink", "autoresize", "autosave", "charmap", "code", "codesample", "directionality", "emoticons", "fullscreen", "image", "importcss", "insertdatetime", "link", "lists", "media", "nonbreaking", "pagebreak", "preview", "quickbars", "save", "searchreplace", "table", "visualblocks", "visualchars", "wordcount"];

export const DEFAULT_TOOLBAR = "undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | link image media table | emoticons charmap | pagebreak anchor | codesample code | preview fullscreen";

export const TOOLBAR_MODE_FLOATING = "floating";
export const TOOLBAR_MODE_SLIDING = "sliding";
export const TOOLBAR_MODE_SCROLLING = "scrolling";
export const TOOLBAR_MODE_WRAP = "wrap";

export const RESIZE_BOTH = "both";

/** App defaults (preserve prior form-type-texteditor behaviour). */
export function editorDefaults() {
    return {
        plugins: [...DEFAULT_PLUGINS],
        language: defaultLanguage(),
        resize: false,
        browser_spellcheck: true,
        toolbar: DEFAULT_TOOLBAR,
        menubar: false,
        contextmenu: false,
        quickbars_selection_toolbar: false,
        quickbars_insert_toolbar: false,
        autoresize_bottom_margin: 0,
        skin: false,
        content_css: false,
        branding: false,
        convert_urls: false,
        license_key: "gpl",
    };
}

/**
 * @param {Element} el
 * @param {Record<string, unknown>} [defaults]
 */
export function editorOptsFromElement(el, defaults = editorDefaults()) {
    const opts = { ...defaults };

    applyPreset(opts, strAttr(el, ATTR.PRESET, null));

    if (el.hasAttribute(ATTR.RESIZE)) {
        const raw = strAttr(el, ATTR.RESIZE, "");
        opts.resize = raw === RESIZE_BOTH ? RESIZE_BOTH : boolAttr(el, ATTR.RESIZE, false);
    }
    if (el.hasAttribute(ATTR.SPELLCHECK)) {
        opts.browser_spellcheck = boolAttr(el, ATTR.SPELLCHECK, true);
    }
    if (el.hasAttribute(ATTR.TOOLBAR)) {
        opts.toolbar = strAttr(el, ATTR.TOOLBAR, DEFAULT_TOOLBAR);
    }
    if (el.hasAttribute(ATTR.MENUBAR)) {
        const raw = strAttr(el, ATTR.MENUBAR, "");
        if (raw === "false" || raw === "0") {
            opts.menubar = false;
        } else if (raw === "true" || raw === "1") {
            opts.menubar = true;
        } else {
            opts.menubar = raw;
        }
    }
    if (el.hasAttribute(ATTR.PLUGINS)) {
        opts.plugins = listAttr(el, ATTR.PLUGINS);
    }
    if (el.hasAttribute(ATTR.MIN_HEIGHT)) {
        opts.min_height = intAttr(el, ATTR.MIN_HEIGHT, 0, 0);
    }
    if (el.hasAttribute(ATTR.MAX_HEIGHT)) {
        opts.max_height = intAttr(el, ATTR.MAX_HEIGHT, 0, 0);
    }
    if (el.hasAttribute(ATTR.HEIGHT)) {
        applyFixedHeight(opts, intAttr(el, ATTR.HEIGHT, 0, 0), {
            pluginsExplicit: el.hasAttribute(ATTR.PLUGINS),
            minHeightExplicit: el.hasAttribute(ATTR.MIN_HEIGHT),
        });
    }
    if (el.hasAttribute(ATTR.LANGUAGE)) {
        opts.language = strAttr(el, ATTR.LANGUAGE, opts.language);
    }
    const placeholder = strAttr(el, ATTR.PLACEHOLDER, null);
    if (placeholder !== null && placeholder !== "") {
        opts.placeholder = placeholder;
    }
    if (el.hasAttribute(ATTR.READONLY)) {
        opts.readonly = boolAttr(el, ATTR.READONLY, false);
    }
    if (el.hasAttribute(ATTR.BRANDING)) {
        opts.branding = boolAttr(el, ATTR.BRANDING, false);
    }
    if (el.hasAttribute(ATTR.ELEMENTPATH)) {
        opts.elementpath = boolAttr(el, ATTR.ELEMENTPATH, true);
    }
    if (el.hasAttribute(ATTR.STATUSBAR)) {
        opts.statusbar = boolAttr(el, ATTR.STATUSBAR, true);
    }
    if (el.hasAttribute(ATTR.TOOLBAR_MODE)) {
        opts.toolbar_mode = strAttr(el, ATTR.TOOLBAR_MODE, TOOLBAR_MODE_FLOATING);
    }
    if (el.hasAttribute(ATTR.TOOLBAR_STICKY)) {
        opts.toolbar_sticky = boolAttr(el, ATTR.TOOLBAR_STICKY, false);
    }
    if (el.hasAttribute(ATTR.AUTORESIZE_BOTTOM_MARGIN)) {
        opts.autoresize_bottom_margin = intAttr(el, ATTR.AUTORESIZE_BOTTOM_MARGIN, 0, 0);
    }
    if (el.hasAttribute(ATTR.CONVERT_URLS)) {
        opts.convert_urls = boolAttr(el, ATTR.CONVERT_URLS, false);
    }
    if (el.hasAttribute(ATTR.PASTE_AS_TEXT)) {
        opts.paste_as_text = boolAttr(el, ATTR.PASTE_AS_TEXT, false);
    }
    if (el.hasAttribute(ATTR.CONTEXTMENU)) {
        const raw = strAttr(el, ATTR.CONTEXTMENU, "");
        opts.contextmenu = raw === "false" || raw === "0" ? false : raw;
    }
    if (el.hasAttribute(ATTR.QUICKBARS_SELECTION_TOOLBAR)) {
        const raw = strAttr(el, ATTR.QUICKBARS_SELECTION_TOOLBAR, "");
        opts.quickbars_selection_toolbar = raw === "false" || raw === "0" ? false : raw;
    }
    if (el.hasAttribute(ATTR.QUICKBARS_INSERT_TOOLBAR)) {
        const raw = strAttr(el, ATTR.QUICKBARS_INSERT_TOOLBAR, "");
        opts.quickbars_insert_toolbar = raw === "false" || raw === "0" ? false : raw;
    }

    const extraContentStyle = el.hasAttribute(ATTR.CONTENT_STYLE) ? strAttr(el, ATTR.CONTENT_STYLE, "") : "";
    opts.content_style = [editorContentStyle(), extraContentStyle].filter(Boolean).join(" ");

    return opts;
}

/**
 * @param {HTMLElement} el
 * @param {Record<string, unknown>} [opts]
 */
export function createEditor(el, opts = editorOptsFromElement(el)) {
    return tinymce.init({
        ...opts,
        target: el,
        setup: (editor) => {
            editor.on("init", () => applyEditorContentTheme(editor));
        },
    });
}

let themeBound = false;

/** Call once from the entrypoint `onReady`. */
export function bindThemeSync() {
    if (themeBound) {
        return;
    }
    themeBound = true;
    syncTinyMceSkin();
    new MutationObserver(syncTinyMceSkin).observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["data-bs-theme"],
    });
}

function defaultLanguage() {
    return appLocale("es");
}

/**
 * @param {Record<string, unknown>} opts
 * @param {string|null} preset
 */
function applyPreset(opts, preset) {
    if (!preset || !PRESETS[preset]) {
        return;
    }
    const bundle = PRESETS[preset];
    for (const [key, value] of Object.entries(bundle)) {
        opts[key] = Array.isArray(value) ? [...value] : value;
    }
}

/** CSV or space-separated list. */
function listAttr(el, name) {
    const raw = strAttr(el, name);
    if (raw === null || raw === "") {
        return [];
    }
    if (raw.includes(",")) {
        return raw
            .split(",")
            .map((part) => part.trim())
            .filter(Boolean);
    }
    return raw
        .split(/\s+/)
        .map((part) => part.trim())
        .filter(Boolean);
}

/**
 * @param {unknown} plugins
 * @returns {string[]}
 */
function normalizePlugins(plugins) {
    if (Array.isArray(plugins)) {
        return plugins.map(String).filter(Boolean);
    }
    if (typeof plugins !== "string" || plugins === "") {
        return [];
    }
    return plugins
        .split(/[,\s]+/)
        .map((part) => part.trim())
        .filter(Boolean);
}

/**
 * @param {Record<string, unknown>} opts
 * @param {number} height
 * @param {{ pluginsExplicit: boolean, minHeightExplicit: boolean }} flags
 */
function applyFixedHeight(opts, height, { pluginsExplicit, minHeightExplicit }) {
    opts.height = height;

    const plugins = normalizePlugins(opts.plugins);
    if (!plugins.includes("autoresize")) {
        return;
    }

    if (!pluginsExplicit) {
        // Default plugin set: fixed height wins over autoresize.
        opts.plugins = plugins.filter((name) => name !== "autoresize");
        return;
    }

    // Custom plugins still use autoresize — height is ignored by TinyMCE; use min_height.
    if (!minHeightExplicit) {
        opts.min_height = height;
    }
}

function isDarkTheme() {
    return document.documentElement.getAttribute("data-bs-theme") === "dark";
}

function resolvedTablerValue(property, variable) {
    const probe = document.createElement("span");
    probe.style[property] = `var(${variable})`;
    document.documentElement.appendChild(probe);
    const value = getComputedStyle(probe)[property];
    probe.remove();

    return value;
}

function tablerContentTheme() {
    return {
        background: resolvedTablerValue("backgroundColor", "--tblr-bg-surface"),
        color: resolvedTablerValue("color", "--tblr-body-color"),
        fontFamily: resolvedTablerValue("fontFamily", "--tblr-body-font-family"),
    };
}

function editorContentStyle() {
    const { background, color, fontFamily } = tablerContentTheme();
    // content_css is disabled for Tabler theming; resolve CSS vars in the parent
    // document and inject computed values (iframe cannot see --tblr-* otherwise).
    // Placeholder ::before is normally shipped in the content skin.
    return [`body { background-color: ${background}; color: ${color}; font-family: ${fontFamily}; position: relative; }`, `.mce-content-body[data-mce-placeholder]:not(.mce-visualblocks)::before {`, `content: attr(data-mce-placeholder); position: absolute; opacity: 0.5;`, `pointer-events: none; color: ${color};`, `}`].join(" ");
}

function applyEditorContentTheme(editor) {
    const body = editor.getBody?.();
    if (!body) {
        return;
    }
    const { background, color, fontFamily } = tablerContentTheme();
    body.style.backgroundColor = background;
    body.style.color = color;
    body.style.fontFamily = fontFamily;
}

function syncTinyMceSkin() {
    const dark = isDarkTheme();
    document.querySelectorAll('link[rel="stylesheet"]').forEach((link) => {
        const href = link.href;
        if (href.includes("/skins/ui/oxide-dark/")) {
            link.disabled = !dark;
        } else if (href.includes("/skins/ui/oxide/")) {
            link.disabled = dark;
        }
    });
    tinymce.get().forEach(applyEditorContentTheme);
}
