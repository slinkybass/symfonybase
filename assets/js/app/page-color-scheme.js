class ColorSchemeHandler {
	#colorSchemeCookieKey;

	constructor() {
		this.#colorSchemeCookieKey = 'colorScheme';
	}

	updateColorScheme() {
		const selectedColorScheme = this.#getCookie(this.#colorSchemeCookieKey) || 'light';
		this.#setColorScheme(selectedColorScheme);
	}

	createColorSchemeSelector() {
		if (null === document.querySelector('input[type="checkbox"][data-color-scheme]')) {
			return;
		}

		const switchSchemeCheckboxes = document.querySelectorAll('input[type="checkbox"][data-color-scheme]');
		switchSchemeCheckboxes.forEach((switchSchemeCheckbox) => {
			switchSchemeCheckbox.addEventListener('change', () => {
				const selectedColorScheme = switchSchemeCheckbox.checked ? 'dark' : 'light';
				this.#setColorScheme(selectedColorScheme);
				switchSchemeCheckboxes.forEach((otherSwitchSchemeCheckbox) => { otherSwitchSchemeCheckbox.checked = switchSchemeCheckbox.checked });
			});
		});
	}

	#setColorScheme(colorScheme) {
		document.body.setAttribute("data-bs-theme", colorScheme);
		this.#setCookie(this.#colorSchemeCookieKey, colorScheme);
		document.body.style.colorScheme = colorScheme;
	}

	#setCookie(name, value, days = 365) {
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days*24*60*60*1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value || "")  + expires + "; path=/";
	}

	#getCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}
}

const colorSchemeHandler = new ColorSchemeHandler();

window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
	colorSchemeHandler.updateColorScheme();
});

document.addEventListener('DOMContentLoaded', () => {
	colorSchemeHandler.createColorSchemeSelector();
});
