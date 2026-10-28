/**
 * Shared bootstrap for `assets/js/fields/form-type-*.js`.
 *
 * Autor: slinkybass
 * Version: 1.0
 *
 * Not a field. Each plugin stays one entrypoint file; this module is infra.
 *
 * Conventions for upcoming plugins:
 * - Flag: data-{name}-field present and not "false"/"0" (PHP json_encode(true) → "true")
 * - Guard: data-{name}-initialized (via dataset key, e.g. dateInitialized)
 * - Bool attrs: omitted → fallback; "false" | "0" → false; any other value → true
 * - Re-init on ea.collection.item-added
 */

export function boolAttr(el, name, fallback = false) {
    if (!el.hasAttribute(name)) {
        return fallback;
    }
    const value = el.getAttribute(name);
    return value !== "false" && value !== "0";
}

export function strAttr(el, name, fallback = null) {
    return el.hasAttribute(name) ? el.getAttribute(name) : fallback;
}

export function intAttr(el, name, fallback = 1, min = 1) {
    if (!el.hasAttribute(name)) {
        return fallback;
    }
    const parsed = Number.parseInt(String(el.getAttribute(name)), 10);
    return Number.isFinite(parsed) && parsed >= min ? parsed : fallback;
}

export function floatAttr(el, name, fallback) {
    if (!el.hasAttribute(name)) {
        return fallback;
    }
    const parsed = Number.parseFloat(String(el.getAttribute(name)).replace(",", "."));
    return Number.isFinite(parsed) ? parsed : fallback;
}

export function bindPlugin(init, { onReady } = {}) {
    document.addEventListener("DOMContentLoaded", () => {
        if (typeof onReady === "function") {
            onReady();
        }
        init();
    });
    document.addEventListener("ea.collection.item-added", () => {
        init();
    });
}

export function eachUninitialized(selector, datasetKey, callback) {
    document.querySelectorAll(selector).forEach((el) => {
        if (el.dataset[datasetKey] !== undefined) {
            return;
        }
        el.dataset[datasetKey] = "";
        callback(el);
    });
}

/**
 * Registers `window[globalName]`, default selector `[attr]`, and collection re-init.
 * Skips elements whose flag is "false" / "0" (`enablePlugin(false)`).
 *
 * @param {string} globalName
 * @param {{ attr: string, datasetKey: string, setup: (el: Element) => void, onReady?: () => void }} options
 */
export function definePlugin(globalName, { attr, datasetKey, setup, onReady }) {
    const defaultSelector = `[${attr}]`;

    function init(selector = defaultSelector) {
        document.querySelectorAll(selector).forEach((el) => {
            if (el.dataset[datasetKey] !== undefined) {
                return;
            }
            if (!boolAttr(el, attr, false)) {
                return;
            }
            el.dataset[datasetKey] = "";
            setup(el);
        });
    }

    bindPlugin(() => init(), { onReady });
    window[globalName] = init;

    return init;
}
