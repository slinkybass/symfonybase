/**
 * Password Visibility & Generator Plugin
 *
 * Autor: slinkybass
 * Version: 3.0
 *
 * Description:
 * Manages password field visibility and provides secure password generation.
 *
 * Events:
 * - DOMContentLoaded: Initializes listeners on page load.
 * - ea.collection.item-added: Re-initializes for dynamically added elements (e.g., EasyAdmin collections).
 *
 * Core Functionality:
 * - The toggle button must have class `.btn-pass` and specify the target input(s)
 *   using `data-input="inputId"` and optionally `data-input2="inputId2"`.
 * - The toggle button can include elements with `.btn-pass-show` and `.btn-pass-hide`
 *   classes to indicate visible/hidden states.
 * - The password generator button must have class `.btn-pass-generator` and
 *   similarly use `data-input` and `data-input2` attributes.
 * - The `minlength` and `maxlength` attributes on inputs define password length range.
 *
 * HTML Examples:
 *
 * Basic password with show/hide toggle:
 * <input type="password" minlength="8" maxlength="12" id="password" />
 * <button type="button" class="btn-pass" data-input="password">
 *   <i class="btn-pass-show"></i>
 *   <i class="btn-pass-hide d-none"></i>
 * </button>
 *
 * Repeated password with password generator:
 * <input type="password" minlength="8" maxlength="12" id="password_first" />
 * <input type="password" minlength="8" maxlength="12" id="password_second" />
 * <button type="button" class="btn-pass-generator" data-input="password_first" data-input2="password_second">
 *   <i></i>
 * </button>
 */

(function () {
    document.addEventListener("DOMContentLoaded", () => {
        formTypePassword();
    });

    document.addEventListener("ea.collection.item-added", () => {
        formTypePassword();
    });

    /**
     * Initializes password show/hide and random password generation functionality.
     *
     * Called on DOMContentLoaded and ea.collection.item-added.
     */
    function formTypePassword() {
        document.querySelectorAll(".btn-pass").forEach((btn) => {
            if (btn.dataset.passwordToggleInitialized !== undefined) {
                return;
            }

            btn.dataset.passwordToggleInitialized = "";
            btn.addEventListener("click", () => {
                const inputSelector = btn.dataset.input;
                const inputSelector2 = btn.dataset.input2;
                const input = inputSelector ? document.getElementById(inputSelector) : null;
                const input2 = inputSelector2 ? document.getElementById(inputSelector2) : null;

                if (!input) {
                    return;
                }

                const isPassword = input.type === "password";

                switchVisibility(input, isPassword);
                if (input2) {
                    switchVisibility(input2, isPassword);
                }
            });
        });

        document.querySelectorAll(".btn-pass-generator").forEach((btn) => {
            if (btn.dataset.passwordGeneratorInitialized !== undefined) {
                return;
            }

            btn.dataset.passwordGeneratorInitialized = "";
            btn.addEventListener("click", () => {
                const inputSelector = btn.dataset.input;
                const inputSelector2 = btn.dataset.input2;
                const input = inputSelector ? document.getElementById(inputSelector) : null;
                const input2 = inputSelector2 ? document.getElementById(inputSelector2) : null;

                if (!input) {
                    return;
                }

                const minLength1 = input.getAttribute("minlength");
                const minLength2 = input2?.getAttribute("minlength");
                const maxLength1 = input.getAttribute("maxlength");
                const maxLength2 = input2?.getAttribute("maxlength");

                const minLen = Math.max(Number.parseInt(String(minLength1 ?? ""), 10) || 0, Number.parseInt(String(minLength2 ?? ""), 10) || 0);
                const maxLen = Math.max(Number.parseInt(String(maxLength1 ?? ""), 10) || 0, Number.parseInt(String(maxLength2 ?? ""), 10) || 0);
                const minLength = minLen > 0 ? minLen : undefined;
                const maxLength = maxLen > 0 ? maxLen : undefined;
                let length;
                if (minLength !== undefined && maxLength !== undefined && maxLength >= minLength) {
                    length = Math.floor(Math.random() * (maxLength - minLength + 1)) + minLength;
                } else {
                    length = minLength ?? maxLength ?? undefined;
                }
                const newPassword = generatePassword(length);

                input.value = newPassword;
                switchVisibility(input);

                if (input2) {
                    input2.value = newPassword;
                    switchVisibility(input2);
                }
            });
        });
    }

    /**
     * Toggles the input field type between "text" and "password" and switches visibility icons.
     *
     * @param {HTMLInputElement} input - The password input field.
     * @param {boolean} show - `true` to show, `false` to hide.
     */
    function switchVisibility(input, show = true) {
        input.type = show ? "text" : "password";

        if (!input.id) {
            return;
        }

        const toggleButton = document.querySelector(`[data-input='${CSS.escape(input.id)}'], [data-input2='${CSS.escape(input.id)}']`);
        if (!toggleButton) {
            return;
        }

        const btnShow = toggleButton.querySelector(".btn-pass-show");
        const btnHide = toggleButton.querySelector(".btn-pass-hide");

        if (btnShow && btnHide) {
            btnShow.classList.toggle("d-none", show);
            btnHide.classList.toggle("d-none", !show);
        }
    }

    /**
     * Generates a random password with uppercase, lowercase, digits, and special characters.
     *
     * @param {number} length - Desired password length.
     * @returns {string} Generated password.
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
     * Selects `n` random characters from the string.
     *
     * @param {number} n - Number of characters to select.
     * @returns {string} Selected characters.
     */
    function pickStr(str, n) {
        let chars = "";
        for (let i = 0; i < n; i++) {
            chars += str.charAt(Math.floor(Math.random() * str.length));
        }
        return chars;
    }

    /**
     * Randomly shuffles the characters of the string.
     *
     * @param {string} str - The string to shuffle.
     * @returns {string} The shuffled string.
     */
    function shuffleStr(str) {
        const array = [...str];
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array.join("");
    }

    window.formTypePassword = formTypePassword;
})();
