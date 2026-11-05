/**
 * slugify helpers for the slug field plugin.
 *
 * Autor: slinkybass
 * Version: 1.0
 *
 * HTML contract. Bool: omitted → default; "false"|"0" → false; else true.
 * Attr present → overrides default.
 *
 * Mapped (slugify options): replacement, lower, strict, trim, locale, remove
 * (RegExp source string).
 *
 * App UI (theme + input): confirm-text; lock button data-slug-lock + icon attrs.
 * Target field ids: data-slug-target (JSON; set by Twig from EasyAdmin `target`).
 *
 * Not mapped: per-field `extend` (global char map; applied once via
 * `applySlugifyExtend()` with EasyAdmin’s currency/symbol empties).
 */

import slugify from "slugify";
import { boolAttr, strAttr } from "./plugin.js";

export const ATTR = {
    PLUGIN: "data-slug-field",
    TARGET: "data-slug-target",
    CONFIRM_TEXT: "data-slug-confirm-text",
    LOCK: "data-slug-lock",
    ICON_LOCKED: "data-slug-icon-locked",
    ICON_UNLOCKED: "data-slug-icon-unlocked",
    REPLACEMENT: "data-slug-replacement",
    LOWER: "data-slug-lower",
    STRICT: "data-slug-strict",
    TRIM: "data-slug-trim",
    LOCALE: "data-slug-locale",
    REMOVE: "data-slug-remove",
};

/** EasyAdmin-compatible defaults (stricter `remove` than slugify’s built-in). */
export const DEFAULT_REMOVE = /[^A-Za-z0-9\s-]/g;

/** Char map emptied once (same as EasyAdmin `field-slug.js`). */
export const DEFAULT_EXTEND = {
    $: "",
    "%": "",
    "&": "",
    "<": "",
    ">": "",
    "|": "",
    "¢": "",
    "£": "",
    "¤": "",
    "¥": "",
    "₠": "",
    "₢": "",
    "₣": "",
    "₤": "",
    "₥": "",
    "₦": "",
    "₧": "",
    "₨": "",
    "₩": "",
    "₪": "",
    "₫": "",
    "€": "",
    "₭": "",
    "₮": "",
    "₯": "",
    "₰": "",
    "₱": "",
    "₲": "",
    "₳": "",
    "₴": "",
    "₵": "",
    "₸": "",
    "₹": "",
    "₽": "",
    "₿": "",
    "∂": "",
    "∆": "",
    "∑": "",
    "∞": "",
    "♥": "",
    元: "",
    円: "",
    "﷼": "",
};

export function slugifyDefaults() {
    return {
        remove: DEFAULT_REMOVE,
        lower: true,
        strict: true,
        trim: true,
        replacement: "-",
    };
}

/**
 * @param {Element} el
 * @param {Record<string, unknown>} [defaults]
 */
export function slugOptsFromElement(el, defaults = slugifyDefaults()) {
    const opts = { ...defaults };

    if (el.hasAttribute(ATTR.REPLACEMENT)) {
        opts.replacement = strAttr(el, ATTR.REPLACEMENT, opts.replacement);
    }
    if (el.hasAttribute(ATTR.LOWER)) {
        opts.lower = boolAttr(el, ATTR.LOWER, true);
    }
    if (el.hasAttribute(ATTR.STRICT)) {
        opts.strict = boolAttr(el, ATTR.STRICT, true);
    }
    if (el.hasAttribute(ATTR.TRIM)) {
        opts.trim = boolAttr(el, ATTR.TRIM, true);
    }
    if (el.hasAttribute(ATTR.LOCALE)) {
        const locale = strAttr(el, ATTR.LOCALE, null);
        if (locale) {
            opts.locale = locale;
        }
    }
    if (el.hasAttribute(ATTR.REMOVE)) {
        const pattern = strAttr(el, ATTR.REMOVE, null);
        if (pattern !== null && pattern !== "") {
            try {
                opts.remove = new RegExp(pattern);
            } catch {
                opts.remove = DEFAULT_REMOVE;
            }
        }
    }

    return opts;
}

/** Call once from the entrypoint `onReady`. */
export function applySlugifyExtend(charMap = DEFAULT_EXTEND) {
    slugify.extend(charMap);
}

/**
 * @param {HTMLElement} field
 * @param {Record<string, unknown>} [opts]
 */
export function createSlugger(field, opts = slugOptsFromElement(field)) {
    const targets = resolveTargets(field);
    if (!targets) {
        return null;
    }

    let locked = true;
    let currentSlug = "";
    field.setAttribute("readonly", "readonly");

    if ("" === field.value) {
        updateValue();
        for (const target of targets) {
            target.addEventListener("input", () => {
                if ("readonly" === field.getAttribute("readonly")) {
                    updateValue();
                }
            });
        }
    } else {
        currentSlug = field.value;
    }

    const lockButton =
        field.parentNode?.querySelector(`[${ATTR.LOCK}]`) ?? field.parentNode?.querySelector("button");
    if (!lockButton) {
        return { updateValue, lock, unlock };
    }

    lockButton.addEventListener("click", () => {
        if (locked) {
            const confirmMessage = strAttr(field, ATTR.CONFIRM_TEXT, null);
            if (null === confirmMessage) {
                unlock();
            } else {
                Swal.fire({
                    html: confirmMessage,
                    icon: "question",
                    allowOutsideClick: false,
                    showDenyButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        unlock();
                    }
                });
            }
        } else {
            lock();
        }
    });

    function updateValue() {
        field.value = slugify(targets.map((target) => target.value).join("-"), opts);
    }

    function unlock() {
        locked = false;
        lockButton.innerHTML = lockButton.getAttribute(ATTR.ICON_UNLOCKED) ?? "";
        lockButton.setAttribute("aria-pressed", "false");
        field.removeAttribute("readonly");
    }

    function lock() {
        locked = true;
        lockButton.innerHTML = lockButton.getAttribute(ATTR.ICON_LOCKED) ?? "";
        lockButton.setAttribute("aria-pressed", "true");

        if ("" !== currentSlug) {
            field.value = currentSlug;
        } else {
            updateValue();
        }

        field.setAttribute("readonly", "readonly");
    }

    return { updateValue, lock, unlock };
}

/**
 * @param {HTMLElement} field
 * @returns {HTMLElement[]|null}
 */
function resolveTargets(field) {
    let fieldNames;
    try {
        fieldNames = JSON.parse(strAttr(field, ATTR.TARGET, "null") ?? "null");
    } catch {
        console.error("Invalid JSON in slug field data-slug-target attribute.");
        return null;
    }
    if (!Array.isArray(fieldNames)) {
        console.error("Slug field data-slug-target must be a JSON array of element ids.");
        return null;
    }

    const targets = [];
    for (const name of fieldNames) {
        const target = document.getElementById(String(name));
        if (null === target) {
            console.error(`Wrong target specified for slug widget ("${name}").`);
            return null;
        }
        targets.push(target);
    }

    return targets;
}
