class ColorSchemeHandler {
    #cookieKey;
    #originX;
    #originY;
    #viewTransition;

    constructor() {
        this.#cookieKey = "colorScheme";
        document.addEventListener(
            "pointerdown",
            (event) => {
                this.#originX = event.clientX;
                this.#originY = event.clientY;
            },
            true,
        );
    }

    /** Applies the cookie, else the EasyAdmin dashboard default, else the rendered theme. Does not write the cookie. */
    applyStoredTheme() {
        this.#applyTheme(this.#resolveScheme());
    }

    initToggleControls() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][data-color-scheme]');
        if (checkboxes.length === 0) {
            return;
        }

        const syncCheckboxes = () => {
            const isDark = document.documentElement.getAttribute("data-bs-theme") === "dark";
            checkboxes.forEach((el) => {
                el.checked = isDark;
            });
        };

        syncCheckboxes();

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", () => {
                const apply = (holdSwitching = false) => {
                    const scheme = checkbox.checked ? "dark" : "light";
                    this.#applyTheme(scheme, holdSwitching);
                    this.#setCookie(this.#cookieKey, scheme);
                    checkboxes.forEach((other) => {
                        other.checked = checkbox.checked;
                    });
                };

                if (!document.startViewTransition || this.#viewTransition) {
                    apply();
                    return;
                }

                const root = document.documentElement;
                root.style.setProperty("--theme-x", `${this.#originX ?? window.innerWidth - 28}px`);
                root.style.setProperty("--theme-y", `${this.#originY ?? 28}px`);
                const transition = document.startViewTransition(() => apply(true));
                this.#viewTransition = transition;
                transition.finished.finally(() => {
                    root.classList.remove("theme-switching");
                    if (this.#viewTransition === transition) {
                        this.#viewTransition = null;
                    }
                });
            });
        });
    }

    #resolveScheme() {
        if ("false" === document.body?.getAttribute("data-ea-dark-scheme-is-enabled")) {
            return "light";
        }

        const stored = this.#getCookie(this.#cookieKey);
        if ("dark" === stored || "light" === stored) {
            return stored;
        }

        const dashboardDefault = document.body?.getAttribute("data-ea-default-color-scheme");
        if ("dark" === dashboardDefault || "light" === dashboardDefault) {
            return dashboardDefault;
        }

        return "dark" === document.documentElement.getAttribute("data-bs-theme") ? "dark" : "light";
    }

    #applyTheme(scheme, holdSwitching = false) {
        const root = document.documentElement;
        root.classList.add("theme-switching");
        root.setAttribute("data-bs-theme", scheme);
        root.style.colorScheme = scheme;
        if (holdSwitching) {
            return;
        }
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                root.classList.remove("theme-switching");
            });
        });
    }

    #setCookie(name, value, days = 365) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            expires = `; expires=${date.toUTCString()}`;
        }
        document.cookie = `${name}=${value || ""}${expires}; path=/; SameSite=Lax`;
    }

    #getCookie(name) {
        const prefix = `${name}=`;
        for (const raw of document.cookie.split(";")) {
            let part = raw.trimStart();
            if (part.startsWith(prefix)) {
                return part.slice(prefix.length);
            }
        }
        return null;
    }
}

const colorSchemeHandler = new ColorSchemeHandler();

colorSchemeHandler.applyStoredTheme();

document.addEventListener("DOMContentLoaded", () => {
    colorSchemeHandler.initToggleControls();
});
