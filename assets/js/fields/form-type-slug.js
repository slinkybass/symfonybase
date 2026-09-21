import slugify from "slugify";

(function () {
    document.addEventListener("DOMContentLoaded", () => {
        formTypeSlug();
    });

    document.addEventListener("ea.collection.item-added", () => {
        formTypeSlug();
    });

    window.formTypeSlug = function formTypeSlug(selector = '[data-slug-field="true"]') {
        document.querySelectorAll(selector).forEach((e) => {
            if (e.dataset.slugInitialized !== undefined) {
                return;
            }

            e.dataset.slugInitialized = "";
            try {
                new Slugger(e);
            } catch (err) {
                console.error(err);
            }
        });
    };

    slugify.extend({
        "$": "",
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
        "元": "",
        "円": "",
        "﷼": "",
    });

    class Slugger {
        constructor(field) {
            this.field = field;
            if (!this.setTargetElement()) {
                return;
            }
            this.locked = true;
            this.field.setAttribute("readonly", "readonly");

            if ("" === this.field.value) {
                this.currentSlug = "";
                this.updateValue();
                this.listenTarget();
            } else {
                this.currentSlug = this.field.value;
            }

            this.appendLockButton();
        }

        setTargetElement() {
            let fieldNames;
            try {
                fieldNames = JSON.parse(this.field.dataset.target ?? "null");
            } catch {
                console.error("Invalid JSON in slug field data-target attribute.");
                return false;
            }
            if (!Array.isArray(fieldNames)) {
                console.error("Slug field data-target must be a JSON array of element ids.");
                return false;
            }

            this.targets = [];

            for (const name of fieldNames) {
                const target = document.getElementById(String(name));

                if (null === target) {
                    console.error(`Wrong target specified for slug widget ("${name}").`);
                    return false;
                }

                this.targets.push(target);
            }

            return true;
        }

        /**
         * Append a "lock" button to control slug behaviour (auto or manual)
         */
        appendLockButton() {
            this.lockButton = this.field.parentNode?.querySelector("button");
            if (!this.lockButton) {
                return;
            }
            this.lockButton.addEventListener("click", () => {
                if (this.locked) {
                    const confirmMessage = this.field.dataset.confirmText || null;
                    if (null === confirmMessage) {
                        this.unlock();
                    } else {
                        Swal.fire({
                            html: confirmMessage,
                            icon: "question",
                            allowOutsideClick: false,
                            showDenyButton: true,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.unlock();
                            }
                        });
                    }
                } else {
                    this.lock();
                }
            });
        }

        /**
         * Unlock the widget input (manual mode)
         */
        unlock() {
            if (!this.lockButton) {
                return;
            }
            this.locked = false;
            this.lockButton.innerHTML = this.lockButton.getAttribute("data-icon-unlocked");
            this.field.removeAttribute("readonly");
        }

        /**
         * Lock the widget input (auto mode)
         */
        lock() {
            if (!this.lockButton) {
                return;
            }
            this.locked = true;
            this.lockButton.innerHTML = this.lockButton.getAttribute("data-icon-locked");

            // Locking it back changes the value either to default value, or recomputes it
            if ("" !== this.currentSlug) {
                this.field.value = this.currentSlug;
            } else {
                this.updateValue();
            }

            this.field.setAttribute("readonly", "readonly");
        }

        updateValue() {
            this.field.value = slugify(this.targets.map((target) => target.value).join("-"), {
                remove: /[^A-Za-z0-9\s-]/g,
                lower: true,
                strict: true,
            });
        }

        /**
         * Observe the target field and slug it
         */
        listenTarget() {
            for (const target of this.targets) {
                target.addEventListener("input", () => {
                    if ("readonly" === this.field.getAttribute("readonly")) {
                        this.updateValue();
                    }
                });
            }
        }
    }
})();
