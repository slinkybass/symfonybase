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
        const name = nameInput.value.trim() ? nameInput.value : "Symfony Base";
        const titleParts = document.title.split(" - ");
        const suffix = titleParts.length > 1 ? titleParts[titleParts.length - 1] : null;
        document.title = suffix ? `${name} - ${suffix}` : name;
    }

    function applyPrimaryColorVars() {
        const hexColor = colorInput.value.trim() ? colorInput.value : "#22a6b3";
		
        let styleTag = document.getElementById('color-override');
        if (!styleTag) {
            styleTag = document.createElement('style');
            styleTag.id = 'color-override';
            document.head.appendChild(styleTag);
        }
        styleTag.textContent = `:root, [data-bs-theme] { --tblr-primary: ${hexColor} !important; }`;
    }
});
