/**
 * Ace editor field
 *
 * Autor: slinkybass
 * Version: 3.2
 */

import ace from "ace-builds/src-min-noconflict/ace";

const ACE_VERSION = "1.44.0";
const CDN = `https://cdn.jsdelivr.net/npm/ace-builds@${ACE_VERSION}/src-min-noconflict`;
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
            const maxLinesSafe = Number.isFinite(maxLines) && maxLines > 0 ? Math.max(maxLines, minLinesSafe) : 20;

            e.classList.add("d-none");

            const editorEl = document.createElement("div");
            editorEl.className = "ace-editor";
            editorEl.style.width = "100%";
            parent.insertBefore(editorEl, e.nextSibling);

            const editor = ace.edit(editorEl, {
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

            editor.getSession().setValue(e.value);
            editor.getSession().on("change", () => {
                e.value = editor.getSession().getValue();
            });
        });
    };
})();
