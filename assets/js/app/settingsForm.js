(function () {
	document.addEventListener("DOMContentLoaded", () => {
		var configAppName = document.getElementById("Config_appName");
		if (configAppName) {
			configAppName.addEventListener('input', updateTitle);
			configAppName.addEventListener('change', updateTitle);
		}

		var configAppColor = document.getElementById("Config_appColor");
		if (configAppColor) {
			configAppColor.addEventListener('input', updateColor);
			configAppColor.addEventListener('change', updateColor);
			configAppColor.addEventListener('move', updateColor);
		}

		function updateTitle() {
			var name = configAppName.value ? configAppName.value : 'Symfony 7 Base';
			var splitedTitle = document.title.split(' - ');
			var prefix = splitedTitle.length > 1 ? splitedTitle[splitedTitle.length - 1] : null;
			document.title = name + (prefix ? (' - ' + prefix) : '');
		}

		function updateColor() {
			var hexColor = configAppColor.value ? configAppColor.value : '#7952B3';
			var rgbColor = hexToRgb(hexColor);
			if (rgbColor) {
				const body = document.querySelector('body');
				body.style.setProperty('--tblr-primary', hexColor);
				body.style.setProperty('--tblr-primary-rgb', `${rgbColor.r}, ${rgbColor.g}, ${rgbColor.b}`);
			}
		}
	});
})();

function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

