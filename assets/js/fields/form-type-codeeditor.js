/**
 * Ace editor field
 *
 * Autor: slinkybass
 * Version: 3.1
 */

import ace from "ace-builds/src-min-noconflict/ace";

const CDN = "https://cdn.jsdelivr.net/npm/ace-builds@latest/src-min-noconflict";
ace.config.set("basePath", CDN);
ace.config.set("modePath", CDN);
ace.config.set("themePath", CDN);
ace.config.set("workerPath", CDN);

(function () {
    document.addEventListener("DOMContentLoaded", () => {
        formTypeCodeEditor();
    });
    document.addEventListener("ea.collection.item-added", () => {
        formTypeCodeEditor();
    });

    window.formTypeCodeEditor = function formTypeCodeEditor(selector = '[data-codeeditor-field="true"]') {
        document.querySelectorAll(selector).forEach((e) => {
            if (e.dataset.codeeditorInitialized !== undefined) {
                return;
            }

            const parent = e.parentNode;
            if (!parent) {
                return;
            }

            e.dataset.codeeditorInitialized = "";

            const theme = e.hasAttribute("data-codeeditor-theme") ? e.getAttribute("data-codeeditor-theme") : "chrome";
            const language = e.hasAttribute("data-codeeditor-language") ? e.getAttribute("data-codeeditor-language") : "javascript";
            const tabSizeRaw = e.hasAttribute("data-codeeditor-tab-size") ? e.getAttribute("data-codeeditor-tab-size") : "4";
            const tabSize = Number.parseInt(String(tabSizeRaw), 10);
            const tabSizeSafe = Number.isFinite(tabSize) && tabSize > 0 ? tabSize : 4;
            const indentWithTabs = e.hasAttribute("data-codeeditor-indent-with-tabs") ? e.getAttribute("data-codeeditor-indent-with-tabs") !== "false" : true;
            const showLineNumbers = e.hasAttribute("data-codeeditor-show-line-numbers") ? e.getAttribute("data-codeeditor-show-line-numbers") !== "false" : true;
            const minLinesRaw = e.hasAttribute("data-codeeditor-min-lines") ? e.getAttribute("data-codeeditor-min-lines") : "5";
            const maxLinesRaw = e.hasAttribute("data-codeeditor-max-lines") ? e.getAttribute("data-codeeditor-max-lines") : "20";
            const minLines = Number.parseInt(String(minLinesRaw), 10);
            const maxLines = Number.parseInt(String(maxLinesRaw), 10);
            const minLinesSafe = Number.isFinite(minLines) && minLines > 0 ? minLines : 5;
            const maxLinesSafe = Number.isFinite(maxLines) && maxLines > 0 ? maxLines : 20;

            // Clone and hide the original field
            const clonedE = e.cloneNode(true);
            clonedE.classList.add("d-none");
            parent.insertBefore(clonedE, e);

            const editor = ace.edit(e, {
                theme: `ace/theme/${theme}`,
                mode: `ace/mode/${language}`,
                tabSize: tabSizeSafe,
                useSoftTabs: !indentWithTabs,
                showLineNumbers,
                showGutter: showLineNumbers,
                minLines: minLinesSafe,
                maxLines: maxLinesSafe,
                autoScrollEditorIntoView: true,
                showPrintMargin: false,
            });

            // Set value in and from the cloned field
            editor.getSession().setValue(clonedE.value);
            editor.getSession().on("change", () => {
                clonedE.value = editor.getSession().getValue();
            });

            // Set autosize
            const resizeEditor = (ed) => {
                let h = ed.getSession().getScreenLength() * (ed.renderer.lineHeight + ed.renderer.scrollBar.getWidth());
                h = Math.min(Math.max(h, 120), 200);
                ed.container.style.height = `${h}px`;
                ed.resize();
            };

            resizeEditor(editor);
            editor.on("change", () => resizeEditor(editor));
        });
    };
})();
