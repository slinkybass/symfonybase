/**
 * IMask helpers for the mask plugin.
 *
 * Autor: slinkybass
 * Version: 1.1
 *
 * HTML contract. Bool: omitted → default; "false"|"0" → false; else true.
 * Attr present → overrides default.
 *
 * Mapped: pattern, regex, type (pattern|number), lazy, placeholder-char, display-char,
 * overwrite (true|false|shift), eager (true|false|append|remove), skip-invalid,
 * autofix (true|false|pad), uppercase, prepare (CSV/JSON presets), commit (CSV/JSON),
 * unmask (submit unmaskedValue), definitions (JSON), scale, radix, thousands-separator,
 * pad-fractional-zeros, normalize-zeros, min, max, map-to-radix (JSON).
 *
 * Prepare presets: upper, lower, trim, strip-spaces, digits-only.
 * Commit presets: trim, pad.
 *
 * Not mapped: prepare/prepareChar/commit/validate/format/parse (arbitrary functions),
 * blocks, Function/Dynamic/Enum/Date masks.
 */

import IMask from "imask";
import { boolAttr, floatAttr, intAttr, strAttr } from "./plugin.js";

export const ATTR = {
    PLUGIN: "data-mask-field",
    PATTERN: "data-mask-pattern",
    REGEX: "data-mask-regex",
    TYPE: "data-mask-type",
    LAZY: "data-mask-lazy",
    PLACEHOLDER: "data-mask-placeholder",
    DISPLAY_CHAR: "data-mask-display-char",
    OVERWRITE: "data-mask-overwrite",
    EAGER: "data-mask-eager",
    SKIP_INVALID: "data-mask-skip-invalid",
    AUTOFIX: "data-mask-autofix",
    UPPERCASE: "data-mask-uppercase",
    PREPARE: "data-mask-prepare",
    COMMIT: "data-mask-commit",
    UNMASK: "data-mask-unmask",
    DEFINITIONS: "data-mask-definitions",
    SCALE: "data-mask-scale",
    RADIX: "data-mask-radix",
    THOUSANDS_SEPARATOR: "data-mask-thousands-separator",
    PAD_FRACTIONAL_ZEROS: "data-mask-pad-fractional-zeros",
    NORMALIZE_ZEROS: "data-mask-normalize-zeros",
    MIN: "data-mask-min",
    MAX: "data-mask-max",
    MAP_TO_RADIX: "data-mask-map-to-radix",
};

export const TYPE_NUMBER = "number";

export const PREPARE_UPPER = "upper";
export const PREPARE_LOWER = "lower";
export const PREPARE_TRIM = "trim";
export const PREPARE_STRIP_SPACES = "strip-spaces";
export const PREPARE_DIGITS_ONLY = "digits-only";

export const COMMIT_TRIM = "trim";
export const COMMIT_PAD = "pad";

function triAttr(el, name, allowed = {}) {
    if (!el.hasAttribute(name)) {
        return undefined;
    }
    const raw = String(el.getAttribute(name));
    if (raw === "false" || raw === "0") {
        return false;
    }
    if (raw === "true" || raw === "1") {
        return true;
    }
    if (allowed[raw] !== undefined) {
        return allowed[raw];
    }
    return raw;
}

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

/** CSV or JSON string array → unique non-empty strings. */
function listAttr(el, name) {
    const raw = strAttr(el, name);
    if (raw === null || raw === "") {
        return [];
    }
    if (raw.trimStart().startsWith("[")) {
        const parsed = jsonAttr(el, name);
        return Array.isArray(parsed) ? parsed.map(String).filter(Boolean) : [];
    }
    return raw
        .split(",")
        .map((part) => part.trim())
        .filter(Boolean);
}

function resolveMask(el) {
    const type = strAttr(el, ATTR.TYPE, null);
    if (type === TYPE_NUMBER) {
        return Number;
    }

    const pattern = strAttr(el, ATTR.PATTERN);
    if (!pattern) {
        return null;
    }

    if (boolAttr(el, ATTR.REGEX, false)) {
        try {
            return new RegExp(pattern);
        } catch {
            return null;
        }
    }

    return pattern;
}

function preparePresets(el) {
    const presets = new Set(listAttr(el, ATTR.PREPARE));
    if (boolAttr(el, ATTR.UPPERCASE, false)) {
        presets.add(PREPARE_UPPER);
    }
    return [...presets];
}

function applyPreparePresets(opts, presets) {
    const charFns = [];
    const batchFns = [];

    for (const preset of presets) {
        switch (preset) {
            case PREPARE_UPPER:
                charFns.push((ch) => String(ch).toUpperCase());
                break;
            case PREPARE_LOWER:
                charFns.push((ch) => String(ch).toLowerCase());
                break;
            case PREPARE_TRIM:
                batchFns.push((str) => String(str).trim());
                break;
            case PREPARE_STRIP_SPACES:
                batchFns.push((str) => String(str).replace(/\s+/g, ""));
                break;
            case PREPARE_DIGITS_ONLY:
                batchFns.push((str) => String(str).replace(/\D+/g, ""));
                break;
            default:
                break;
        }
    }

    if (charFns.length > 0) {
        opts.prepareChar = (ch) => charFns.reduce((value, fn) => fn(value), ch);
    }
    if (batchFns.length > 0) {
        opts.prepare = (str) => batchFns.reduce((value, fn) => fn(value), str);
    }
}

function applyCommitPresets(opts, presets) {
    if (presets.length === 0) {
        return;
    }

    opts.commit = (value, masked) => {
        let next = value;
        if (presets.includes(COMMIT_TRIM)) {
            next = String(next).trim();
        }
        if (next !== value && typeof masked.value !== "undefined") {
            masked.value = next;
        }
        if (presets.includes(COMMIT_PAD) && typeof masked.pad === "function") {
            masked.pad();
        }
    };
}

function applyDefinitions(opts, definitions) {
    if (!definitions || typeof definitions !== "object" || Array.isArray(definitions)) {
        return;
    }

    const resolved = {};
    for (const [key, value] of Object.entries(definitions)) {
        if (typeof value === "string") {
            try {
                resolved[key] = new RegExp(value);
            } catch {
                resolved[key] = value;
            }
        } else {
            resolved[key] = value;
        }
    }
    opts.definitions = resolved;
}

/** App defaults (preserve prior form-type-mask behaviour). */
export function maskDefaults() {
    return {
        overwrite: false,
        skipInvalid: true,
    };
}

/**
 * @param {Element} el
 * @param {Record<string, unknown>} [defaults]
 */
export function maskOptsFromElement(el, defaults = maskDefaults()) {
    const mask = resolveMask(el);
    if (mask === null) {
        return null;
    }

    const opts = { ...defaults, mask };
    const placeholderChar = strAttr(el, ATTR.PLACEHOLDER);
    if (placeholderChar !== null) {
        opts.placeholderChar = placeholderChar;
    }

    if (el.hasAttribute(ATTR.LAZY)) {
        opts.lazy = boolAttr(el, ATTR.LAZY, true);
    } else if (placeholderChar !== null) {
        // BC: placeholder implies visible slots (lazy false).
        opts.lazy = false;
    }

    if (el.hasAttribute(ATTR.DISPLAY_CHAR)) {
        opts.displayChar = strAttr(el, ATTR.DISPLAY_CHAR);
    }

    if (el.hasAttribute(ATTR.OVERWRITE)) {
        opts.overwrite = triAttr(el, ATTR.OVERWRITE, { shift: "shift" });
    }

    if (el.hasAttribute(ATTR.EAGER)) {
        opts.eager = triAttr(el, ATTR.EAGER, { append: "append", remove: "remove" });
    }

    if (el.hasAttribute(ATTR.SKIP_INVALID)) {
        opts.skipInvalid = boolAttr(el, ATTR.SKIP_INVALID, true);
    }

    if (el.hasAttribute(ATTR.AUTOFIX)) {
        opts.autofix = triAttr(el, ATTR.AUTOFIX, { pad: "pad" });
    }

    applyPreparePresets(opts, preparePresets(el));
    applyCommitPresets(opts, listAttr(el, ATTR.COMMIT));

    if (el.hasAttribute(ATTR.DEFINITIONS)) {
        applyDefinitions(opts, jsonAttr(el, ATTR.DEFINITIONS));
    }

    if (mask === Number) {
        if (el.hasAttribute(ATTR.SCALE)) {
            opts.scale = intAttr(el, ATTR.SCALE, 0, 0);
        }
        if (el.hasAttribute(ATTR.RADIX)) {
            opts.radix = strAttr(el, ATTR.RADIX);
        }
        if (el.hasAttribute(ATTR.THOUSANDS_SEPARATOR)) {
            opts.thousandsSeparator = strAttr(el, ATTR.THOUSANDS_SEPARATOR);
        }
        if (el.hasAttribute(ATTR.PAD_FRACTIONAL_ZEROS)) {
            opts.padFractionalZeros = boolAttr(el, ATTR.PAD_FRACTIONAL_ZEROS, false);
        }
        if (el.hasAttribute(ATTR.NORMALIZE_ZEROS)) {
            opts.normalizeZeros = boolAttr(el, ATTR.NORMALIZE_ZEROS, true);
        }
        if (el.hasAttribute(ATTR.MIN)) {
            opts.min = floatAttr(el, ATTR.MIN, undefined);
        }
        if (el.hasAttribute(ATTR.MAX)) {
            opts.max = floatAttr(el, ATTR.MAX, undefined);
        }
        if (el.hasAttribute(ATTR.MAP_TO_RADIX)) {
            const mapped = jsonAttr(el, ATTR.MAP_TO_RADIX);
            if (mapped !== undefined) {
                opts.mapToRadix = mapped;
            }
        }
    }

    return opts;
}

/** @param {Element} el @param {Record<string, unknown>|null} [opts] */
export function createMask(el, opts = null) {
    const resolved = opts ?? maskOptsFromElement(el);
    if (!resolved) {
        return null;
    }

    const mask = IMask(el, resolved);

    if (boolAttr(el, ATTR.UNMASK, false)) {
        const form = el.closest("form");
        form?.addEventListener(
            "submit",
            () => {
                el.value = mask.unmaskedValue;
            },
            true,
        );
    }

    return mask;
}
