class ColorSchemeHandler {
    #cookieKey;

    constructor() {
        this.#cookieKey = "colorScheme";
    }

    /** Applies theme from cookie (or default light) to `data-bs-theme`. */
    applyStoredTheme() {
        const scheme = this.#getCookie(this.#cookieKey) || "light";
        this.#applyTheme(scheme);
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
                const scheme = checkbox.checked ? "dark" : "light";
                this.#applyTheme(scheme);
                checkboxes.forEach((other) => {
                    other.checked = checkbox.checked;
                });
            });
        });
    }

    #applyTheme(scheme) {
        document.documentElement.setAttribute("data-bs-theme", scheme);
        this.#setCookie(this.#cookieKey, scheme);
    }

    #setCookie(name, value, days = 365) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            expires = `; expires=${date.toUTCString()}`;
        }
        document.cookie = `${name}=${value || ""}${expires}; path=/`;
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

window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", () => {
    colorSchemeHandler.applyStoredTheme();
});

document.addEventListener("DOMContentLoaded", () => {
    colorSchemeHandler.applyStoredTheme();
    colorSchemeHandler.initToggleControls();
});
