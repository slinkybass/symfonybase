document.addEventListener("DOMContentLoaded", () => {
    const configAppName = document.getElementById("Config_appName");
    if (configAppName) {
        configAppName.addEventListener("input", updateTitle);
        configAppName.addEventListener("change", updateTitle);
    }

    const configAppColor = document.getElementById("Config_appColor");
    if (configAppColor) {
        configAppColor.addEventListener("input", updateColor);
        configAppColor.addEventListener("change", updateColor);
        configAppColor.addEventListener("move", updateColor);
    }

    function updateTitle() {
        const name = configAppName.value ? configAppName.value : "Symfony 7 Base";
        const titleParts = document.title.split(" - ");
        const suffix = titleParts.length > 1 ? titleParts[titleParts.length - 1] : null;
        document.title = name + (suffix ? ` - ${suffix}` : "");
    }

    function updateColor() {
        const hexColor = configAppColor.value ? configAppColor.value : "#22a6b3";
        const rgbColor = hexToRgb(hexColor);
        if (rgbColor) {
            const body = document.querySelector("body");
            body.style.setProperty("--tblr-primary", hexColor);
            body.style.setProperty("--tblr-primary-rgb", `${rgbColor.r}, ${rgbColor.g}, ${rgbColor.b}`);
        }
    }
});

function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result
        ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16),
        }
        : null;
}