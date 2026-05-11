class ColorSchemeHandler {
	#colorSchemeCookieKey;

	constructor() {
		this.#colorSchemeCookieKey = "colorScheme";
	}

	updateColorScheme() {
		const selectedColorScheme = this.#getCookie(this.#colorSchemeCookieKey) || "light";
		this.#setColorScheme(selectedColorScheme);
	}

	createColorSchemeSelector() {
		if (document.querySelector('input[type="checkbox"][data-color-scheme]') === null) {
			return;
		}

		const switchSchemeCheckboxes = document.querySelectorAll('input[type="checkbox"][data-color-scheme]');
		switchSchemeCheckboxes.forEach((switchSchemeCheckbox) => {
			switchSchemeCheckbox.addEventListener("change", () => {
				const selectedColorScheme = switchSchemeCheckbox.checked ? "dark" : "light";
				this.#setColorScheme(selectedColorScheme);
				switchSchemeCheckboxes.forEach((otherSwitchSchemeCheckbox) => {
					otherSwitchSchemeCheckbox.checked = switchSchemeCheckbox.checked;
				});
			});
		});
	}

	#setColorScheme(colorScheme) {
		document.documentElement.setAttribute("data-bs-theme", colorScheme);
		this.#setCookie(this.#colorSchemeCookieKey, colorScheme);
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
		const nameEQ = `${name}=`;
		for (const raw of document.cookie.split(";")) {
			let c = raw;
			while (c.startsWith(" ")) {
				c = c.slice(1);
			}
			if (c.startsWith(nameEQ)) {
				return c.slice(nameEQ.length);
			}
		}
		return null;
	}
}

const colorSchemeHandler = new ColorSchemeHandler();

window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", () => {
	colorSchemeHandler.updateColorScheme();
});

document.addEventListener("DOMContentLoaded", () => {
	colorSchemeHandler.createColorSchemeSelector();
});
