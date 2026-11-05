/**
 * noUiSlider helpers for the slider field plugin.
 *
 * Autor: slinkybass
 * Version: 1.0
 *
 * HTML contract. Bool: omitted → default; "false"|"0" → false; else true.
 * Attr present → overrides default.
 *
 * Range/start/step come from native min/max/step/value on the input (Float/Integer
 * setMin/setMax/setStep). Optional data-slider-* overrides those when present.
 *
 * Mapped: show-input, tooltips, connect (lower|upper|true|false|JSON), pips
 * (true → steps mode, or JSON), orientation, direction, behaviour, animate,
 * animation-duration, keyboard-support, keyboard-page-multiplier,
 * keyboard-multiplier, keyboard-default-step, margin, limit, padding (number|JSON),
 * snap (true → % stops from min/max/step; JSON number list → those values only),
 * decimals (format.to/from), height (CSS for vertical).
 *
 * Not mapped: format/ariaFormat as arbitrary functions; multi-handle start arrays;
 * cssPrefix/cssClasses; documentElement; handleAttributes; pips format functions.
 */

import noUiSlider from "nouislider";
import { boolAttr, floatAttr, intAttr, strAttr } from "./plugin.js";

export const ATTR = {
    PLUGIN: "data-slider-field",
    SHOW_INPUT: "data-slider-show-input",
    TOOLTIPS: "data-slider-tooltips",
    CONNECT: "data-slider-connect",
    PIPS: "data-slider-pips",
    ORIENTATION: "data-slider-orientation",
    DIRECTION: "data-slider-direction",
    BEHAVIOUR: "data-slider-behaviour",
    ANIMATE: "data-slider-animate",
    ANIMATION_DURATION: "data-slider-animation-duration",
    KEYBOARD_SUPPORT: "data-slider-keyboard-support",
    KEYBOARD_PAGE_MULTIPLIER: "data-slider-keyboard-page-multiplier",
    KEYBOARD_MULTIPLIER: "data-slider-keyboard-multiplier",
    KEYBOARD_DEFAULT_STEP: "data-slider-keyboard-default-step",
    MARGIN: "data-slider-margin",
    LIMIT: "data-slider-limit",
    PADDING: "data-slider-padding",
    SNAP: "data-slider-snap",
    DECIMALS: "data-slider-decimals",
    HEIGHT: "data-slider-height",
    MIN: "data-slider-min",
    MAX: "data-slider-max",
    STEP: "data-slider-step",
};

export const CONNECT_LOWER = "lower";
export const CONNECT_UPPER = "upper";
export const ORIENTATION_HORIZONTAL = "horizontal";
export const ORIENTATION_VERTICAL = "vertical";
export const DIRECTION_LTR = "ltr";
export const DIRECTION_RTL = "rtl";

const DEFAULT_PIPS = {
    mode: "steps",
    density: 100,
};

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

/**
 * @param {number} decimals
 * @returns {{ to: (value: number) => number, from: (value: number|string) => number }}
 */
function numberFormat(decimals) {
    const places = Number.isFinite(decimals) && decimals >= 0 ? decimals : 2;
    return {
        to: (value) => Number.parseFloat(Number.parseFloat(String(value)).toFixed(places)),
        from: (value) => Number.parseFloat(Number.parseFloat(String(value)).toFixed(places)),
    };
}

/**
 * @param {Element} el
 * @param {string} name
 * @param {number} fallback
 * @returns {number}
 */
function numberFromAttrOrHtml(el, name, htmlName, fallback) {
    if (el.hasAttribute(name)) {
        return floatAttr(el, name, fallback);
    }
    if (el.hasAttribute(htmlName)) {
        const parsed = Number.parseFloat(String(el.getAttribute(htmlName)).replace(",", "."));
        return Number.isFinite(parsed) ? parsed : fallback;
    }
    return fallback;
}

/**
 * @param {Element} el
 * @returns {"lower"|"upper"|boolean|boolean[]|undefined}
 */
function connectFromElement(el) {
    if (!el.hasAttribute(ATTR.CONNECT)) {
        return undefined;
    }
    const raw = String(el.getAttribute(ATTR.CONNECT));
    if (raw === "true" || raw === "1") {
        return true;
    }
    if (raw === "false" || raw === "0") {
        return false;
    }
    if (raw === CONNECT_LOWER || raw === CONNECT_UPPER) {
        return raw;
    }
    if (raw.trimStart().startsWith("[")) {
        const parsed = jsonAttr(el, ATTR.CONNECT);
        if (Array.isArray(parsed)) {
            return parsed;
        }
    }
    return CONNECT_LOWER;
}

/**
 * @param {Element} el
 * @returns {object|undefined}
 */
function pipsFromElement(el) {
    if (!el.hasAttribute(ATTR.PIPS)) {
        return undefined;
    }
    const raw = String(el.getAttribute(ATTR.PIPS));
    if (raw === "false" || raw === "0") {
        return undefined;
    }
    if (raw.trimStart().startsWith("{")) {
        const parsed = jsonAttr(el, ATTR.PIPS);
        if (parsed && typeof parsed === "object") {
            return parsed;
        }
    }
    return { ...DEFAULT_PIPS };
}

/**
 * noUiSlider `snap` only honours values listed in `range`.
 *
 * @param {number[]} values
 * @returns {Record<string, number>}
 */
function snapRangeFromValues(values) {
    const nums = [...new Set(values.map((v) => Number(v)).filter((v) => Number.isFinite(v)))].sort(
        (a, b) => a - b,
    );
    if (nums.length === 0) {
        return { min: 0, max: 100 };
    }
    if (nums.length === 1) {
        return { min: nums[0], max: nums[0] };
    }

    const min = nums[0];
    const max = nums[nums.length - 1];
    const span = max - min;
    const range = { min, max };
    for (let i = 1; i < nums.length - 1; i++) {
        const value = nums[i];
        const pct = span > 0 ? ((value - min) / span) * 100 : (i / (nums.length - 1)) * 100;
        range[`${pct}%`] = Number.parseFloat(value.toPrecision(12));
    }

    return range;
}

/**
 * Even snap stops from a linear min/max/step (fallback when `snap` is boolean true).
 *
 * @param {number} min
 * @param {number} max
 * @param {number} step
 * @returns {Record<string, number>}
 */
function snapRangeFromStep(min, max, step) {
    const values = [min];
    const span = max - min;
    if (span > 0 && step > 0) {
        const steps = Math.round(span / step);
        for (let i = 1; i < steps; i++) {
            values.push(min + i * step);
        }
    }
    values.push(max);
    return snapRangeFromValues(values);
}

/** App defaults (preserve prior form-type-slider behaviour). */
export function sliderDefaults() {
    return {
        tooltips: true,
        connect: CONNECT_LOWER,
    };
}

/**
 * @param {Element} el
 * @param {Record<string, unknown>} [defaults]
 */
export function sliderOptsFromElement(el, defaults = sliderDefaults()) {
    const opts = { ...defaults };

    let min = numberFromAttrOrHtml(el, ATTR.MIN, "min", 0);
    let max = numberFromAttrOrHtml(el, ATTR.MAX, "max", 100);
    if (max < min) {
        const swap = min;
        min = max;
        max = swap;
    }

    let start = Number.parseFloat(String(el instanceof HTMLInputElement ? el.value : "").replace(",", "."));
    if (!Number.isFinite(start)) {
        start = min;
    }
    start = Math.min(max, Math.max(min, start));

    let step = numberFromAttrOrHtml(el, ATTR.STEP, "step", 1);
    if (!(step > 0)) {
        step = 1;
    }

    const decimals = el.hasAttribute(ATTR.DECIMALS) ? intAttr(el, ATTR.DECIMALS, 2, 0) : 2;
    const format = numberFormat(decimals);

    opts.start = start;
    opts.step = step;
    opts.range = { min, max };
    opts.format = format;

    if (el.hasAttribute(ATTR.TOOLTIPS)) {
        opts.tooltips = boolAttr(el, ATTR.TOOLTIPS, true);
    }
    const connect = connectFromElement(el);
    if (connect !== undefined) {
        opts.connect = connect;
    } else if (opts.connect === undefined) {
        opts.connect = CONNECT_LOWER;
    }

    const pips = pipsFromElement(el);
    if (pips) {
        opts.pips = {
            ...pips,
            format: pips.format ?? format,
        };
    }

    if (el.hasAttribute(ATTR.ORIENTATION)) {
        opts.orientation = strAttr(el, ATTR.ORIENTATION);
    }
    if (el.hasAttribute(ATTR.DIRECTION)) {
        opts.direction = strAttr(el, ATTR.DIRECTION);
    }
    if (el.hasAttribute(ATTR.BEHAVIOUR)) {
        opts.behaviour = strAttr(el, ATTR.BEHAVIOUR);
    }
    if (el.hasAttribute(ATTR.ANIMATE)) {
        opts.animate = boolAttr(el, ATTR.ANIMATE, true);
    }
    if (el.hasAttribute(ATTR.ANIMATION_DURATION)) {
        opts.animationDuration = intAttr(el, ATTR.ANIMATION_DURATION, 300, 0);
    }
    if (el.hasAttribute(ATTR.KEYBOARD_SUPPORT)) {
        opts.keyboardSupport = boolAttr(el, ATTR.KEYBOARD_SUPPORT, true);
    }
    if (el.hasAttribute(ATTR.KEYBOARD_PAGE_MULTIPLIER)) {
        opts.keyboardPageMultiplier = floatAttr(el, ATTR.KEYBOARD_PAGE_MULTIPLIER, 5);
    }
    if (el.hasAttribute(ATTR.KEYBOARD_MULTIPLIER)) {
        opts.keyboardMultiplier = floatAttr(el, ATTR.KEYBOARD_MULTIPLIER, 1);
    }
    if (el.hasAttribute(ATTR.KEYBOARD_DEFAULT_STEP)) {
        opts.keyboardDefaultStep = floatAttr(el, ATTR.KEYBOARD_DEFAULT_STEP, 10);
    }
    if (el.hasAttribute(ATTR.MARGIN)) {
        opts.margin = floatAttr(el, ATTR.MARGIN, 0);
    }
    if (el.hasAttribute(ATTR.LIMIT)) {
        opts.limit = floatAttr(el, ATTR.LIMIT, 0);
    }
    if (el.hasAttribute(ATTR.PADDING)) {
        const raw = String(el.getAttribute(ATTR.PADDING) ?? "");
        if (raw.trimStart().startsWith("[")) {
            const parsed = jsonAttr(el, ATTR.PADDING);
            if (parsed !== undefined) {
                opts.padding = parsed;
            }
        } else {
            opts.padding = floatAttr(el, ATTR.PADDING, 0);
        }
    }
    if (el.hasAttribute(ATTR.SNAP)) {
        const raw = String(el.getAttribute(ATTR.SNAP) ?? "");
        if (raw !== "false" && raw !== "0") {
            opts.snap = true;
            delete opts.step;
            if (raw.trimStart().startsWith("[")) {
                const parsed = jsonAttr(el, ATTR.SNAP);
                if (Array.isArray(parsed) && parsed.length > 0) {
                    opts.range = snapRangeFromValues(parsed);
                    const lo = opts.range.min;
                    const hi = opts.range.max;
                    opts.start = Math.min(hi, Math.max(lo, start));
                } else {
                    opts.range = snapRangeFromStep(min, max, step);
                }
            } else {
                opts.range = snapRangeFromStep(min, max, step);
            }
        }
    }

    return opts;
}

/**
 * @param {Element} el
 * @param {Record<string, unknown>} [opts]
 * @returns {object|null}
 */
export function createSlider(el, opts = {}) {
    if (!(el instanceof HTMLInputElement)) {
        return null;
    }

    const parent = el.parentNode;
    if (!parent) {
        return null;
    }

    if (!boolAttr(el, ATTR.SHOW_INPUT, false)) {
        el.classList.add("d-none");
    }

    const slider = document.createElement("div");
    slider.classList.add("slider");
    if (opts.orientation === ORIENTATION_VERTICAL) {
        slider.style.height = strAttr(el, ATTR.HEIGHT, "200px") || "200px";
    }
    parent.insertBefore(slider, el.nextSibling);

    noUiSlider.create(slider, opts);

    slider.noUiSlider.on("update", (values) => {
        el.value = String(values[0]);
    });

    const updateSlider = () => {
        if (!slider.noUiSlider) {
            return;
        }
        const raw = String(el.value ?? "").replace(",", ".");
        const n = Number.parseFloat(raw);
        if (Number.isFinite(n)) {
            slider.noUiSlider.set(n);
        }
    };
    el.addEventListener("change", updateSlider);
    el.addEventListener("input", updateSlider);

    return slider.noUiSlider;
}
