document.addEventListener("DOMContentLoaded", () => {
    const nameInput = document.getElementById("Config_appName");
    if (nameInput) {
        nameInput.addEventListener("input", updateDocumentTitle);
        nameInput.addEventListener("change", updateDocumentTitle);
    }

    const colorInput = document.getElementById("Config_appColor");
    if (colorInput) {
        colorInput.addEventListener("input", applyPrimaryColorVars);
        colorInput.addEventListener("change", applyPrimaryColorVars);
        colorInput.addEventListener("move", applyPrimaryColorVars);
    }

    function updateDocumentTitle() {
        const name = nameInput.value.trim() ? nameInput.value : "Symfony 7 Base";
        const titleParts = document.title.split(" - ");
        const suffix = titleParts.length > 1 ? titleParts[titleParts.length - 1] : null;
        document.title = suffix ? `${name} - ${suffix}` : name;
    }

    function applyPrimaryColorVars() {
        const hex = colorInput.value.trim() ? colorInput.value : "#22a6b3";
        const rgb = hexToRgb(hex);
        if (!rgb) {
            return;
        }
        const body = document.body;
        if (!body) {
            return;
        }
        body.style.setProperty("--tblr-primary", hex);
        body.style.setProperty("--tblr-primary-rgb", `${rgb.r}, ${rgb.g}, ${rgb.b}`);
    }
});

function hexToRgb(hex) {
    const match = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    if (!match) {
        return null;
    }
    return {
        r: parseInt(match[1], 16),
        g: parseInt(match[2], 16),
        b: parseInt(match[3], 16),
    };
}
