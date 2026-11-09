/**
 * EasyAdmin collection/array field — patched fork of
 * `vendor/easycorp/easyadmin-bundle/assets/js/field-collection.js`.
 *
 * Autor: slinkybass
 * Version: 2.0
 *
 * Why a fork (not stock EA JS):
 * - Tabler form theme keeps a single `.ea-form-collection-items` wrapper; empty-state
 *   replacement must not inject a nested wrapper (stock EA does).
 * - Hierarchy (`data-hf-parent` / `data-hf-child`) needs unique group ids per new row.
 * - No `eval()` of prototype `<script>` tags.
 *
 * Keep in sync with EA on upgrades: re-diff upstream, re-apply only the patches
 * marked below. Public contract for the rest of the app:
 * - Markup: `[data-ea-collection-field]`, `.field-collection-item`, add/delete buttons
 * - Events: `ea.collection.item-added` / `ea.collection.item-removed`
 *   detail: `{ newElement, collection }` / `{ deletedElement, collection }`
 *
 * Consumers: `lib/plugin.js` (re-init field plugins), `helpers/hierarchy-fields.js`,
 * `app.js` (TomSelect autocomplete).
 */

import { bindPlugin } from "./lib/plugin.js";

const eaCollectionHandler = () => {
    document.querySelectorAll("button.field-collection-add-button").forEach((addButton) => {
        const collection = addButton.closest("[data-ea-collection-field]");

        if (!collection || collection.dataset.eaProcessed) {
            return;
        }

        EaCollectionProperty.handleAddButton(addButton, collection);
        EaCollectionProperty.updateCollectionItemCssClasses(collection);
    });

    document.querySelectorAll("button.field-collection-delete-button").forEach((deleteButton) => {
        if (deleteButton.dataset.eaProcessed) {
            return;
        }

        deleteButton.dataset.eaProcessed = "true";
        deleteButton.addEventListener("click", () => {
            const collection = deleteButton.closest("[data-ea-collection-field]");
            const item = deleteButton.closest(".field-collection-item");

            if (!item) {
                return;
            }

            item.remove();
            document.dispatchEvent(
                new CustomEvent("ea.collection.item-removed", {
                    detail: { deletedElement: item, collection },
                }),
            );

            EaCollectionProperty.updateCollectionItemCssClasses(collection);
        });
    });
};

bindPlugin(eaCollectionHandler);

const EaCollectionProperty = {
    handleAddButton: (addButton, collection) => {
        addButton.addEventListener("click", function () {
            const isArrayCollection = collection.classList.contains("field-array");
            // Use a counter to avoid having the same index more than once
            let numItems = Number.parseInt(String(collection.dataset.numItems ?? "0"), 10);
            if (!Number.isFinite(numItems) || numItems < 0) {
                numItems = 0;
            }

            const prototypeHtml = collection.dataset.prototype;
            const formTypeNamePlaceholder = collection.dataset.formTypeNamePlaceholder;
            if (typeof prototypeHtml !== "string" || prototypeHtml === "" || typeof formTypeNamePlaceholder !== "string") {
                return;
            }

            // Remove the 'Empty Collection' badge, if present
            // PATCH (Tabler): replace in-place inside existing `.ea-form-collection-items`
            // (stock EA injects another `.ea-form-collection-items` wrapper).
            const emptyCollectionBadge = this.parentElement?.querySelector(".collection-empty") ?? null;
            if (null !== emptyCollectionBadge) {
                emptyCollectionBadge.outerHTML = isArrayCollection
                    ? ""
                    : '<div class="accordion"><div class="form-widget-compound"><div data-empty-collection></div></div></div>';
            }

            const placeholderEscaped = formTypeNamePlaceholder.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
            const labelRegexp = new RegExp(placeholderEscaped + "label__", "g");
            const nameRegexp = new RegExp(placeholderEscaped, "g");

            let newItemHtml = prototypeHtml.replace(labelRegexp, String(numItems)).replace(nameRegexp, String(numItems));

            // PATCH (hierarchy): uniquify hf group names so rows do not share parents/children.
            newItemHtml = patchHierarchyGroupIds(newItemHtml, numItems);

            const newItemInsertionSelector = isArrayCollection
                ? ".ea-form-collection-items"
                : ".ea-form-collection-items .accordion > .form-widget-compound [data-empty-collection]";
            const collectionItemsWrapper = collection.querySelector(newItemInsertionSelector);

            if (!collectionItemsWrapper) {
                return;
            }

            collectionItemsWrapper.insertAdjacentHTML("beforeend", newItemHtml);
            collection.dataset.numItems = String(numItems + 1);

            // PATCH: no eval of prototype <script> (stock EA runs them).

            const collectionItems = collectionItemsWrapper.querySelectorAll(".field-collection-item");
            const lastElement = collectionItems[collectionItems.length - 1] ?? null;

            // for complex collections of items, show the newly added item as not collapsed
            if (!isArrayCollection && lastElement) {
                EaCollectionProperty.updateCollectionItemCssClasses(collection);
                const lastElementCollapseButton = lastElement.querySelector(".accordion-button");
                const lastElementBody = lastElement.querySelector(".accordion-collapse");
                if (lastElementCollapseButton) {
                    lastElementCollapseButton.classList.remove("collapsed");
                }
                if (lastElementBody) {
                    lastElementBody.classList.add("show");
                }
            }

            document.dispatchEvent(
                new CustomEvent("ea.collection.item-added", {
                    detail: { newElement: lastElement, collection },
                }),
            );
        });

        collection.dataset.eaProcessed = "true";
    },

    updateCollectionItemCssClasses: (collection) => {
        if (null === collection) {
            return;
        }

        const collectionItems = collection.querySelectorAll(".field-collection-item");
        collectionItems.forEach((item) => item.classList.remove("field-collection-item-first", "field-collection-item-last"));

        const firstElement = collectionItems[0];
        if (undefined === firstElement) {
            return;
        }
        firstElement.classList.add("field-collection-item-first");

        const lastElement = collectionItems[collectionItems.length - 1];
        if (undefined === lastElement) {
            return;
        }
        lastElement.classList.add("field-collection-item-last");
    },
};

/**
 * Append `_${index}` to data-hf-parent / data-hf-child values in prototype HTML.
 *
 * @param {string} html
 * @param {number} index
 * @returns {string}
 */
function patchHierarchyGroupIds(html, index) {
    const suffix = `_${index}`;
    return html
        .replace(/data-hf-parent="([^"]*)"/g, (_m, group) => `data-hf-parent="${group}${suffix}"`)
        .replace(/data-hf-child="([^"]*)"/g, (_m, group) => `data-hf-child="${group}${suffix}"`);
}
