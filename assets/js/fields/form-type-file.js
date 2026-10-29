/**
 * File field
 *
 * Autor: slinkybass
 * Version: 3.3
 *
 * Contract: data-file-field on the upload wrapper (ea_fileupload_widget).
 */

import { definePlugin } from "./lib/plugin.js";

definePlugin("formTypeFile", {
    attr: "data-file-field",
    datasetKey: "fileInitialized",
    setup(el) {
        const input = el.querySelector('input[type="file"]');
        if (!input) {
            return;
        }

        const deleteCheckbox = el.querySelector('input[type="checkbox"][id$="_delete"]');
        const deleteBtn = el.querySelector(".file-delete-btn");
        const previewFiles = el.querySelector(".img-preview");

        deleteBtn?.addEventListener("click", () => {
            if (!deleteCheckbox) {
                return;
            }

            deleteCheckbox.checked = true;
            input.title = "";
            previewFiles?.remove();
            deleteBtn.remove();
        });
    },
});
