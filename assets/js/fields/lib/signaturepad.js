/**
 * SignaturePad helpers for the signature field plugin.
 *
 * Autor: slinkybass
 * Version: 1.0
 *
 * HTML contract. Bool: omitted → default; "false"|"0" → false; else true.
 * Attr present → overrides SignaturePad / app default.
 *
 * Mapped (SignaturePad Options): pen-color, background-color, dot-size, min-width,
 * max-width, throttle, min-distance, velocity-filter-weight, composite-operation,
 * canvas-context-options (JSON).
 *
 * App UI (theme + input): show-input (default false), show-undo / show-clear
 * (theme; default true). Button hooks: data-signature-undo / data-signature-clear.
 *
 * Export: format (image/png|image/jpeg|image/svg+xml), quality (JPEG 0–1).
 *
 * Not mapped: dotSize as function; beginStroke/beforeUpdateStroke/afterUpdateStroke/
 * endStroke hooks; fromData point-group overrides.
 */

import SignaturePad from "@tabler/core/dist/libs/signature_pad/dist/signature_pad.umd.min.js";
import { boolAttr, floatAttr, intAttr, strAttr } from "./plugin.js";

export const ATTR = {
    PLUGIN: "data-signature-field",
    SHOW_INPUT: "data-signature-show-input",
    SHOW_UNDO: "data-signature-show-undo",
    SHOW_CLEAR: "data-signature-show-clear",
    UNDO: "data-signature-undo",
    CLEAR: "data-signature-clear",
    PEN_COLOR: "data-signature-pen-color",
    BACKGROUND_COLOR: "data-signature-background-color",
    DOT_SIZE: "data-signature-dot-size",
    MIN_WIDTH: "data-signature-min-width",
    MAX_WIDTH: "data-signature-max-width",
    THROTTLE: "data-signature-throttle",
    MIN_DISTANCE: "data-signature-min-distance",
    VELOCITY_FILTER_WEIGHT: "data-signature-velocity-filter-weight",
    COMPOSITE_OPERATION: "data-signature-composite-operation",
    CANVAS_CONTEXT_OPTIONS: "data-signature-canvas-context-options",
    FORMAT: "data-signature-format",
    QUALITY: "data-signature-quality",
};

export const FORMAT_PNG = "image/png";
export const FORMAT_JPEG = "image/jpeg";
export const FORMAT_SVG = "image/svg+xml";

const FLOAT_MAP = [
    [ATTR.DOT_SIZE, "dotSize"],
    [ATTR.MIN_WIDTH, "minWidth"],
    [ATTR.MAX_WIDTH, "maxWidth"],
    [ATTR.VELOCITY_FILTER_WEIGHT, "velocityFilterWeight"],
];

const INT_MAP = [
    [ATTR.THROTTLE, "throttle"],
    [ATTR.MIN_DISTANCE, "minDistance"],
];

const STR_MAP = [
    [ATTR.PEN_COLOR, "penColor"],
    [ATTR.BACKGROUND_COLOR, "backgroundColor"],
    [ATTR.COMPOSITE_OPERATION, "compositeOperation"],
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

/** @param {Element} el @param {Record<string, unknown>} [defaults] */
export function padOptsFromElement(el, defaults = {}) {
    const opts = { ...defaults };

    for (const [attr, key] of FLOAT_MAP) {
        if (el.hasAttribute(attr)) {
            opts[key] = floatAttr(el, attr, opts[key]);
        }
    }
    for (const [attr, key] of INT_MAP) {
        if (el.hasAttribute(attr)) {
            opts[key] = intAttr(el, attr, opts[key] ?? 0, 0);
        }
    }
    for (const [attr, key] of STR_MAP) {
        if (el.hasAttribute(attr)) {
            opts[key] = strAttr(el, attr);
        }
    }
    if (el.hasAttribute(ATTR.CANVAS_CONTEXT_OPTIONS)) {
        const parsed = jsonAttr(el, ATTR.CANVAS_CONTEXT_OPTIONS);
        if (parsed !== undefined) {
            opts.canvasContextOptions = parsed;
        }
    }

    return opts;
}

/**
 * @param {Element} el
 * @param {Record<string, unknown>} [opts]
 * @returns {SignaturePad|null}
 */
export function createPad(el, opts = {}) {
    if (!(el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement)) {
        return null;
    }

    const wrapper = el.parentNode;
    const canvas = wrapper?.querySelector(".signature-pad-wrapper canvas");
    if (!(canvas instanceof HTMLCanvasElement)) {
        return null;
    }

    const currentSignValue = el.value;
    const canvasActions = wrapper?.querySelector(".signature-pad-actions");
    const signaturePad = new SignaturePad(canvas, opts);
    syncCanvasSurface(canvas, signaturePad.backgroundColor);

    if (!boolAttr(el, ATTR.SHOW_INPUT, false)) {
        el.classList.add("d-none");
    }

    resizeCanvas(el, signaturePad);
    window.addEventListener("resize", () => resizeCanvas(el, signaturePad));
    window.addEventListener("shown.bs.tab", () => resizeCanvas(el, signaturePad));

    signaturePad.addEventListener("afterUpdateStroke", () => setValue(el, signaturePad));

    if (!canvasActions) {
        return signaturePad;
    }

    const undoBtn = canvasActions.querySelector(`[${ATTR.UNDO}]`);
    const clearBtn = canvasActions.querySelector(`[${ATTR.CLEAR}]`);

    if (undoBtn instanceof HTMLButtonElement || undoBtn instanceof HTMLElement) {
        if ("disabled" in undoBtn) {
            undoBtn.disabled = true;
        }
        signaturePad.addEventListener("afterUpdateStroke", () => {
            if ("disabled" in undoBtn) {
                undoBtn.disabled = signaturePad.isEmpty();
            }
        });
        undoBtn.addEventListener("click", () => {
            const data = signaturePad.toData();
            if (!data) {
                return;
            }
            data.pop();
            signaturePad.fromData(data);
            if ("disabled" in undoBtn) {
                undoBtn.disabled = signaturePad.isEmpty();
            }
            if (signaturePad.isEmpty()) {
                el.value = currentSignValue || "";
                setSignature(el, signaturePad);
            } else {
                setValue(el, signaturePad);
            }
            if (clearBtn && "disabled" in clearBtn) {
                clearBtn.disabled = !el.value;
            }
        });
    }

    if (clearBtn instanceof HTMLButtonElement || clearBtn instanceof HTMLElement) {
        if ("disabled" in clearBtn) {
            clearBtn.disabled = signaturePad.isEmpty();
        }
        signaturePad.addEventListener("afterUpdateStroke", () => {
            if ("disabled" in clearBtn) {
                clearBtn.disabled = signaturePad.isEmpty();
            }
        });
        clearBtn.addEventListener("click", () => {
            if ("disabled" in clearBtn) {
                clearBtn.disabled = true;
            }
            if (undoBtn && "disabled" in undoBtn) {
                undoBtn.disabled = false;
            }
            signaturePad.clear();
            el.value = "";
        });
    }

    return signaturePad;
}

/** @returns {number} */
function devicePixelRatio() {
    return Math.max(window.devicePixelRatio || 1, 1);
}

/**
 * CSS uses `--tblr-bg-forms` on the canvas; override when SignaturePad has a real fill.
 *
 * @param {HTMLCanvasElement} canvas
 * @param {string|undefined} backgroundColor
 */
function syncCanvasSurface(canvas, backgroundColor) {
    if (backgroundColor && !isTransparentColor(backgroundColor)) {
        canvas.style.backgroundColor = backgroundColor;
    } else {
        canvas.style.removeProperty("background-color");
    }
}

/**
 * @param {string|undefined|null} color
 * @returns {boolean}
 */
function isTransparentColor(color) {
    if (!color || color === "transparent") {
        return true;
    }
    const match = String(color).match(/^rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)(?:\s*,\s*([\d.]+))?\s*\)$/i);
    if (match && match[4] !== undefined) {
        return Number.parseFloat(match[4]) === 0;
    }
    return false;
}

/**
 * JPEG has no alpha: use the pad fill, or white when the pad is transparent.
 *
 * @param {string|undefined} backgroundColor
 * @returns {string}
 */
function jpegBackground(backgroundColor) {
    if (!backgroundColor || isTransparentColor(backgroundColor)) {
        return "#ffffff";
    }
    return backgroundColor;
}

/**
 * @param {HTMLInputElement|HTMLTextAreaElement} el
 * @param {InstanceType<typeof SignaturePad>} signaturePad
 */
function resizeCanvas(el, signaturePad) {
    const canvas = signaturePad.canvas;

    if (!canvas || canvas.offsetWidth <= 0 || canvas.offsetHeight <= 0) {
        return;
    }

    const ratio = devicePixelRatio();
    const ctx = canvas.getContext("2d");
    if (!ctx) {
        return;
    }

    const data = signaturePad.toData();
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    // Setting width/height clears the bitmap; clear() re-applies backgroundColor.
    signaturePad.clear();

    if (data.length > 0) {
        signaturePad.fromData(data);
    } else {
        setSignature(el, signaturePad);
    }
}

/**
 * @param {HTMLInputElement|HTMLTextAreaElement} el
 * @param {InstanceType<typeof SignaturePad>} signaturePad
 */
function setSignature(el, signaturePad) {
    if (!el.value) {
        return;
    }
    const canvas = signaturePad.canvas;
    const currentSignImg = new Image();
    currentSignImg.onload = () => {
        const cssW = canvas.offsetWidth;
        const cssH = canvas.offsetHeight;
        const imgW = currentSignImg.width;
        const imgH = currentSignImg.height;
        if (imgW <= 0 || imgH <= 0 || cssW <= 0 || cssH <= 0) {
            return;
        }
        const scale = Math.min(cssW / imgW, cssH / imgH, 1);
        const drawW = imgW * scale;
        const drawH = imgH * scale;
        signaturePad.fromDataURL(el.value, {
            width: drawW,
            height: drawH,
            xOffset: (cssW - drawW) / 2,
            yOffset: (cssH - drawH) / 2,
        });
    };
    currentSignImg.src = el.value;
}

/**
 * @param {HTMLInputElement|HTMLTextAreaElement} el
 * @returns {{ format: string, quality: number|undefined }}
 */
function exportOpts(el) {
    const format = strAttr(el, ATTR.FORMAT, FORMAT_PNG) || FORMAT_PNG;
    let quality;
    if (el.hasAttribute(ATTR.QUALITY)) {
        quality = floatAttr(el, ATTR.QUALITY, 0.92);
    }
    return { format, quality };
}

/**
 * @param {HTMLInputElement|HTMLTextAreaElement} el
 * @param {InstanceType<typeof SignaturePad>} signaturePad
 */
function setValue(el, signaturePad) {
    if (signaturePad.isEmpty()) {
        el.value = "";
        return;
    }

    const { format, quality } = exportOpts(el);

    if (format === FORMAT_SVG) {
        el.value = signaturePad.toDataURL(FORMAT_SVG) || "";
        return;
    }

    // Always crop from PNG so JPEG does not bake transparent pixels into black.
    const dataUrl = signaturePad.toDataURL(FORMAT_PNG);
    const background = format === FORMAT_JPEG ? jpegBackground(signaturePad.backgroundColor) : null;

    cropDataURL(dataUrl, format, quality, background)
        .then((newDataUrl) => {
            el.value = newDataUrl || "";
        })
        .catch(() => {
            el.value = "";
        });
}

/**
 * @param {string} dataUrl
 * @param {string} format
 * @param {number|undefined} quality
 * @param {string|null} background
 * @returns {Promise<string>}
 */
function cropDataURL(dataUrl, format = FORMAT_PNG, quality, background = null) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            if (!ctx) {
                resolve("");
                return;
            }
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);

            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const pixels = imageData.data;
            let minX = canvas.width;
            let minY = canvas.height;
            let maxX = -1;
            let maxY = -1;

            for (let y = 0; y < canvas.height; y++) {
                for (let x = 0; x < canvas.width; x++) {
                    const index = (y * canvas.width + x) * 4;
                    const a = pixels[index + 3];
                    if (a !== 0) {
                        minX = Math.min(minX, x);
                        minY = Math.min(minY, y);
                        maxX = Math.max(maxX, x);
                        maxY = Math.max(maxY, y);
                    }
                }
            }

            if (maxX < minX || maxY < minY) {
                resolve("");
                return;
            }

            const width = maxX - minX + 1;
            const height = maxY - minY + 1;
            const padding = 15;
            const newCanvas = document.createElement("canvas");
            const newCtx = newCanvas.getContext("2d");
            if (!newCtx) {
                resolve("");
                return;
            }
            newCanvas.width = width + 2 * padding;
            newCanvas.height = height + 2 * padding;
            if (format === FORMAT_JPEG) {
                newCtx.fillStyle = background || "#ffffff";
                newCtx.fillRect(0, 0, newCanvas.width, newCanvas.height);
            } else {
                newCtx.clearRect(0, 0, newCanvas.width, newCanvas.height);
            }
            newCtx.drawImage(canvas, minX, minY, width, height, padding, padding, width, height);

            resolve(
                format === FORMAT_JPEG
                    ? newCanvas.toDataURL(FORMAT_JPEG, quality)
                    : newCanvas.toDataURL(FORMAT_PNG),
            );
        };
        img.onerror = reject;
        img.src = dataUrl;
    });
}
