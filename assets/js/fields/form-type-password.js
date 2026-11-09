/**
 * Password field
 *
 * Autor: slinkybass
 * Version: 4.0
 *
 * Contract: data-password-field is the group id (theme replaces "true").
 * Buttons join it with data-password-toggle / data-password-generate.
 * A toggle's first .icon is "show", the second is "hide".
 * Length comes from minlength/maxlength.
 */

import { boolAttr, definePlugin } from "./lib/plugin.js";

const FIELD = "data-password-field";
const TOGGLE = "data-password-toggle";
const GENERATE = "data-password-generate";

definePlugin("formTypePassword", {
    attr: FIELD,
    datasetKey: "passwordInitialized",
    setup(el) {
        bindPasswordGroup(el.getAttribute(FIELD));
    },
});

/** Bind every toggle and generator that shares this group. Safe to call again. */
function bindPasswordGroup(group) {
    if (!group || groupInputs(group).length === 0) {
        return;
    }

    for (const button of [...groupButtons(TOGGLE, group), ...groupButtons(GENERATE, group)]) {
        if (button.dataset.passwordActionInitialized !== undefined) {
            continue;
        }

        const isToggle = button.hasAttribute(TOGGLE);
        button.dataset.passwordActionInitialized = "";
        button.addEventListener("click", () => {
            const inputs = groupInputs(group);
            if (inputs.length === 0) {
                return;
            }

            if (!isToggle) {
                const newPassword = generatePassword(passwordLength(inputs));
                for (const input of inputs) {
                    input.value = newPassword;
                    input.dispatchEvent(new Event("input", { bubbles: true }));
                    input.dispatchEvent(new Event("change", { bubbles: true }));
                }
            }

            const show = !isToggle || inputs[0].type === "password";
            setVisible(inputs, groupButtons(TOGGLE, group), show);
        });
    }
}

/**
 * @param {string} group
 * @returns {HTMLInputElement[]}
 */
function groupInputs(group) {
    return [...document.querySelectorAll(`input[${FIELD}="${CSS.escape(group)}"]`)].filter(
        (input) => input instanceof HTMLInputElement && boolAttr(input, FIELD, false),
    );
}

/**
 * @param {string} attr
 * @param {string} group
 * @returns {Element[]}
 */
function groupButtons(attr, group) {
    return [...document.querySelectorAll(`[${attr}="${CSS.escape(group)}"]`)];
}

/**
 * @param {HTMLInputElement[]} inputs
 * @returns {number|undefined}
 */
function passwordLength(inputs) {
    const minLen = Math.max(...inputs.map((input) => attrLength(input, "minlength")));
    const maxLen = Math.max(...inputs.map((input) => attrLength(input, "maxlength")));
    const minLength = minLen > 0 ? minLen : undefined;
    const maxLength = maxLen > 0 ? maxLen : undefined;

    if (minLength !== undefined && maxLength !== undefined && maxLength >= minLength) {
        return randomInt(maxLength - minLength + 1) + minLength;
    }

    return minLength ?? maxLength ?? undefined;
}

/**
 * @param {HTMLInputElement|null} input
 * @param {string} name
 * @returns {number}
 */
function attrLength(input, name) {
    if (!input) {
        return 0;
    }
    return Number.parseInt(String(input.getAttribute(name) ?? ""), 10) || 0;
}

/**
 * @param {HTMLInputElement[]} inputs
 * @param {Element[]} toggles
 * @param {boolean} show
 */
function setVisible(inputs, toggles, show) {
    for (const input of inputs) {
        input.type = show ? "text" : "password";
    }

    for (const toggle of toggles) {
        const [showIcon, hideIcon] = toggle.querySelectorAll(".icon");
        if (showIcon && hideIcon) {
            showIcon.classList.toggle("d-none", show);
            hideIcon.classList.toggle("d-none", !show);
        }
    }
}

/**
 * Random password with uppercase, lowercase, digits, and one special.
 *
 * @param {number} [length]
 * @returns {string}
 */
function generatePassword(length = 8) {
    let targetLen = typeof length === "number" ? length : Number.parseInt(String(length), 10);
    if (!Number.isFinite(targetLen) || targetLen < 1) {
        targetLen = 8;
    }
    if (targetLen < 4) {
        targetLen = 4;
    }

    const uppercase = "ABCDEFGHJKLMNPQRSTUVWXYZ";
    const lowercase = "abcdefghijkmnpqrstuvwxyz";
    const numbers = "23456789";
    const specials = "!@#$%&_";
    const all = uppercase + lowercase + numbers;

    let password = "";
    password += pickStr(specials, 1);
    password += pickStr(lowercase, 1);
    password += pickStr(uppercase, 1);
    password += pickStr(numbers, 1);
    if (targetLen > password.length) {
        password += pickStr(all, targetLen - password.length);
    }
    return shuffleStr(password);
}

/**
 * @param {string} str
 * @param {number} n
 * @returns {string}
 */
function pickStr(str, n) {
    let chars = "";
    for (let i = 0; i < n; i++) {
        chars += str.charAt(randomInt(str.length));
    }
    return chars;
}

/**
 * Unbiased integer in [0, maxExclusive).
 *
 * @param {number} maxExclusive
 * @returns {number}
 */
function randomInt(maxExclusive) {
    if (maxExclusive <= 1) {
        return 0;
    }
    const buf = new Uint32Array(1);
    const limit = Math.floor(0x100000000 / maxExclusive) * maxExclusive;
    let x;
    do {
        crypto.getRandomValues(buf);
        x = buf[0];
    } while (x >= limit);
    return x % maxExclusive;
}

/**
 * @param {string} str
 * @returns {string}
 */
function shuffleStr(str) {
    const array = [...str];
    for (let i = array.length - 1; i > 0; i--) {
        const j = randomInt(i + 1);
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array.join("");
}
