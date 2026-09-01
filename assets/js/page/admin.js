import App from "./app.js";
import Mark from "mark.js";

function sanitizeUrl(url, requireSameOrigin = false) {
    if (url === null || url === "") {
        return null;
    }

    try {
        const parsedUrl = new URL(url, window.location.origin);
        const allowedProtocols = ["http:", "https:", "mailto:", "tel:"];
        if (!allowedProtocols.includes(parsedUrl.protocol)) {
            return null;
        }

        if (requireSameOrigin && parsedUrl.origin !== window.location.origin) {
            return null;
        }

        return url;
    } catch {
        return null;
    }
}

function toggleVisibilityClasses(element, removeVisibility) {
    if (removeVisibility) {
        element.classList.remove("d-block");
        element.classList.add("d-none");
    } else {
        element.classList.remove("d-none");
        element.classList.add("d-block");
    }
}

function createFilterToggles() {
    document.querySelectorAll(".filter-checkbox").forEach((filterCheckbox) => {
        filterCheckbox.addEventListener("change", () => {
            const toggleSibling = filterCheckbox.nextElementSibling;
            if (!toggleSibling) {
                return;
            }
            const filterExpandedAttribute = toggleSibling.getAttribute("aria-expanded");

            if ((filterCheckbox.checked && "false" === filterExpandedAttribute) || (!filterCheckbox.checked && "true" === filterExpandedAttribute)) {
                toggleSibling.click();
            }
        });
    });

    document.querySelectorAll("form[data-ea-filters-form-id]").forEach((form) => {
        // TODO: when using the native datepicker, 'change' isn't fired unless you input the entire date + time information
        form.addEventListener("change", (event) => {
            if (event.target.classList.contains("filter-checkbox")) {
                return;
            }

            const filterField = event.target.closest(".filter-field");
            if (!filterField) {
                return;
            }

            const filterCheckbox = filterField.querySelector(".filter-checkbox");
            if (filterCheckbox && !filterCheckbox.checked) {
                filterCheckbox.checked = true;
            }
        });
    });

    document.querySelectorAll("[data-ea-comparison-id]").forEach((comparisonWidget) => {
        comparisonWidget.addEventListener("change", (event) => {
            const widget = event.currentTarget;
            const comparisonId = widget.dataset.eaComparisonId;
            if (comparisonId === undefined) {
                return;
            }
            const secondValue = document.querySelector(`[data-ea-value2-of-comparison-id="${comparisonId}"]`);
            if (secondValue === null) {
                return;
            }
            toggleVisibilityClasses(secondValue, widget.value !== "between");
        });
    });
}

function removeHashFromUrl() {
    if (!window.location.href.includes("#")) {
        return;
    }

    const body = document.body;
    if (!body?.classList.contains("ea-index")) {
        return;
    }

    // don't set the hash to '' because that also removes the query parameters
    const urlParts = window.location.href.split("#");
    const urlWithoutHash = urlParts[0];
    window.history.replaceState({}, "", urlWithoutHash);
}

function createSearchHighlight() {
    const searchElement = document.querySelector('.form-action-search [name="query"]');
    if (null === searchElement) {
        return;
    }

    const searchQuery = searchElement.value;
    if ("" === searchQuery.trim()) {
        return;
    }

    // splits a string into tokens, taking into account quoted strings
    // Example: 'foo "bar baz" qux' => ['foo', 'bar baz', 'qux']
    const tokenizeString = (string) => {
        const regex = /"([^"\\]*(\\.[^"\\]*)*)"|\S+/g;
        const tokens = [];

        let match = regex.exec(string);
        while (null !== match) {
            tokens.push(match[0].replaceAll('"', "").trim());
            match = regex.exec(string);
        }

        return tokens;
    };

    const searchQueryTerms = tokenizeString(searchQuery);

    const elementsToHighlight = document.querySelectorAll("table tbody td.searchable");
    const highlighter = new Mark(elementsToHighlight);
    highlighter.mark(searchQueryTerms, { separateWordSearch: false });
}

function createFilters() {
    const filterButton = document.querySelector(".datagrid-filters .action-filters-button");
    if (null === filterButton) {
        return;
    }

    const filterModalSelector = filterButton.getAttribute("data-bs-target");
    const filterModal = filterModalSelector ? document.querySelector(filterModalSelector) : null;
    if (null === filterModal) {
        return;
    }

    const filtersUrl = sanitizeUrl(filterButton.getAttribute("data-href"), true);
    if (null === filtersUrl) {
        return;
    }

    // this is needed to avoid errors when connection is slow
    filterButton.setAttribute("href", filtersUrl);
    filterButton.removeAttribute("data-href");
    filterButton.classList.remove("disabled");

    filterButton.addEventListener("click", (event) => {
        event.preventDefault();

        const filterModalBody = filterModal.querySelector(".modal-body");
        if (!filterModalBody) {
            return;
        }

        filterModalBody.innerHTML = '<div class="w-100 text-center"><div class="spinner-border text-primary" role="status"></div></div>';

        fetch(filterButton.getAttribute("href"))
            .then((response) => response.text())
            .then((text) => {
                filterModalBody.innerHTML = text;
                App.createAutoCompleteFields();
                createFilterToggles();
            })
            .catch((error) => {
                console.error(error);
            });
    });

    const removeFilter = (filterField) => {
        const form = filterField.closest("form");
        if (!form) {
            return;
        }

        form.querySelectorAll(`input[name^="filters[${filterField.dataset.filterProperty}]"]`).forEach((filterFieldInput) => {
            filterFieldInput.remove();
        });

        filterField.remove();
    };

    const submitFilterModalForm = () => {
        const form = filterModal.querySelector("form");
        form?.submit();
    };

    const modalClearButton = document.querySelector("#modal-clear-button");
    if (null !== modalClearButton) {
        modalClearButton.addEventListener("click", () => {
            filterModal.querySelectorAll(".filter-field").forEach((filterField) => {
                removeFilter(filterField);
            });
            submitFilterModalForm();
        });
    }

    const modalApplyButton = document.querySelector("#modal-apply-button");
    if (null !== modalApplyButton) {
        modalApplyButton.addEventListener("click", () => {
            filterModal.querySelectorAll(".filter-checkbox:not(:checked)").forEach((notAppliedFilter) => {
                const field = notAppliedFilter.closest(".filter-field");
                if (field) {
                    removeFilter(field);
                }
            });
            submitFilterModalForm();
        });
    }
}

function createBatchActions() {
    let lastUpdatedRowCheckbox = null;
    const selectAllCheckbox = document.querySelector(".form-batch-checkbox-all");
    if (null === selectAllCheckbox) {
        return;
    }

    const rowCheckboxes = document.querySelectorAll('input[type="checkbox"].form-batch-checkbox');
    selectAllCheckbox.addEventListener("change", () => {
        rowCheckboxes.forEach((rowCheckbox) => {
            rowCheckbox.checked = selectAllCheckbox.checked;
            rowCheckbox.dispatchEvent(new Event("change"));
        });
    });

    const deselectAllButton = document.querySelector(".deselect-batch-button");
    if (null !== deselectAllButton) {
        deselectAllButton.addEventListener("click", () => {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.dispatchEvent(new Event("change"));
        });
    }

    rowCheckboxes.forEach((rowCheckbox, rowCheckboxIndex) => {
        rowCheckbox.dataset.rowIndex = rowCheckboxIndex;

        rowCheckbox.addEventListener("click", (e) => {
            if (lastUpdatedRowCheckbox && e.shiftKey) {
                const lastIndex = Number.parseInt(lastUpdatedRowCheckbox.dataset.rowIndex);
                const currentIndex = Number.parseInt(e.target.dataset.rowIndex);
                const valueToApply = e.target.checked;
                const lowest = Math.min(lastIndex, currentIndex);
                const highest = Math.max(lastIndex, currentIndex);

                rowCheckboxes.forEach((rowCheckbox2, rowCheckboxIndex2) => {
                    if (lowest <= rowCheckboxIndex2 && rowCheckboxIndex2 <= highest) {
                        rowCheckbox2.checked = valueToApply;
                        rowCheckbox2.dispatchEvent(new Event("change"));
                    }
                });
            }
            lastUpdatedRowCheckbox = e.target;
        });

        rowCheckbox.addEventListener("change", () => {
            const selectedRowCheckboxes = document.querySelectorAll('input[type="checkbox"].form-batch-checkbox:checked');
            const row = rowCheckbox.closest("tr");
            const content = rowCheckbox.closest(".page-wrapper");

            if (row) {
                if (rowCheckbox.checked) {
                    row.classList.add("selected-row");
                } else {
                    row.classList.remove("selected-row");
                    selectAllCheckbox.checked = false;
                }
            } else if (!rowCheckbox.checked) {
                selectAllCheckbox.checked = false;
            }

            const rowsAreSelected = 0 !== selectedRowCheckboxes.length;
            const contentTitle = document.querySelector(".content-header-title > .title");
            const filters = content?.querySelector(".datagrid-filters");
            const globalActions = content?.querySelector(".global-actions");
            const batchActions = content?.querySelector(".batch-actions");

            if (null !== contentTitle) {
                toggleVisibilityClasses(contentTitle, rowsAreSelected);
            }
            if (null !== filters) {
                toggleVisibilityClasses(filters, rowsAreSelected);
            }
            if (null !== globalActions) {
                toggleVisibilityClasses(globalActions, rowsAreSelected);
            }
            if (null !== batchActions) {
                toggleVisibilityClasses(batchActions, !rowsAreSelected);
            }
        });
    });

    const modalTitle = document.querySelector("#batch-action-confirmation-title");
    const titleContentWithPlaceholders = modalTitle?.textContent;
    const batchConfirmButton = document.querySelector("#modal-batch-action-button");

    document.querySelectorAll("[data-action-batch]").forEach((dataActionBatch) => {
        dataActionBatch.addEventListener("click", (event) => {
            event.preventDefault();

            const actionElement = event.currentTarget;
            const selectedItems = document.querySelectorAll('input[type="checkbox"].form-batch-checkbox:checked');

            const submitBatchAction = () => {
                // prevent double submission of the batch action form
                actionElement.setAttribute("disabled", "disabled");

                const batchFormFields = {
                    batchActionName: actionElement.getAttribute("data-action-name"),
                    entityFqcn: actionElement.getAttribute("data-entity-fqcn"),
                    batchActionUrl: actionElement.getAttribute("data-action-url"),
                    batchActionCsrfToken: actionElement.getAttribute("data-action-csrf-token"),
                };
                selectedItems.forEach((item, i) => {
                    batchFormFields[`batchActionEntityIds[${i}]`] = item.value;
                });

                const batchForm = document.createElement("form");
                batchForm.setAttribute("method", "POST");
                batchForm.setAttribute("action", actionElement.getAttribute("data-action-url"));
                for (const fieldName in batchFormFields) {
                    const formField = document.createElement("input");
                    formField.setAttribute("type", "hidden");
                    formField.setAttribute("name", fieldName);
                    formField.setAttribute("value", batchFormFields[fieldName]);
                    batchForm.appendChild(formField);
                }

                document.body.appendChild(batchForm);
                batchForm.submit();
            };

            // check if this batch action should skip confirmation
            if (actionElement.hasAttribute("data-action-batch-no-confirm")) {
                submitBatchAction();
            } else if (null !== modalTitle && null !== batchConfirmButton) {
                // show confirmation modal
                const actionName = actionElement.textContent.trim() || actionElement.getAttribute("title");

                // use custom message if provided, otherwise use default modal title
                const customMessage = actionElement.getAttribute("data-batch-action-confirm-message");
                const messageTemplate = customMessage ?? titleContentWithPlaceholders;

                modalTitle.textContent = messageTemplate
                    .replace("%action_name%", actionName)
                    .replace("%num_items%", selectedItems.length.toString());

                batchConfirmButton.addEventListener("click", submitBatchAction, { once: true });
            }
        });
    });
}

function createActionConfirmationModals() {
    const modalStatus = document.querySelector("#action-confirmation-status");
    const modalIcon = document.querySelector("#action-confirmation-icon");
    const modalTitle = document.querySelector("#action-confirmation-title");
    const modalButton = document.querySelector("#modal-action-confirmation-button");
    const modalActionLabel = modalButton?.querySelector(".action-label");
    const modalActionIcon = modalButton?.querySelector(".icon");

    if (!modalStatus || !modalIcon || !modalTitle || !modalButton || !modalActionLabel || !modalActionIcon) {
        return;
    }

    const defaultTitleTemplate = modalTitle.textContent;
    const defaultButtonLabel = modalActionLabel.textContent;
    const defaultButtonIcon = modalActionIcon.cloneNode(true);
    const variantToClass = {
        default: "btn-secondary",
        primary: "btn-primary",
        success: "btn-success",
        warning: "btn-warning",
        danger: "btn-danger",
    };
    const allVariantClasses = Object.values(variantToClass);
    const variantTxtToClass = {
        default: "text-secondary",
        primary: "text-primary",
        success: "text-success",
        warning: "text-warning",
        danger: "text-danger",
    };
    const allVariantTxtClasses = Object.values(variantTxtToClass);
    const variantBgToClass = {
        default: "bg-secondary",
        primary: "bg-primary",
        success: "bg-success",
        warning: "bg-warning",
        danger: "bg-danger",
    };
    const allVariantBgClasses = Object.values(variantBgToClass);

    document.querySelectorAll('[data-action-confirmation="true"]').forEach((actionElement) => {
        actionElement.addEventListener("click", (event) => {
            event.preventDefault();

            const actionName = actionElement.textContent.trim() || actionElement.getAttribute("title");
            const entityName = actionElement.getAttribute("data-action-entity-name") || "";
            const entityId = actionElement.getAttribute("data-action-entity-id") || "";

            // use custom message if provided, otherwise use default modal title
            const customMessage = actionElement.getAttribute("data-action-confirmation-message");
            const messageTemplate = customMessage ?? defaultTitleTemplate;

            modalTitle.textContent = messageTemplate
                .replace("%action_name%", actionName)
                .replace("%entity_name%", entityName)
                .replace("%entity_id%", entityId);

            // use custom button label if provided, otherwise use default
            const customButtonLabel = actionElement.getAttribute("data-action-confirmation-button") ?? actionName;
            modalActionLabel.textContent = customButtonLabel ?? defaultButtonLabel;

            // use custom button icon if provided, otherwise use default
            const customButtonIcon = actionElement.querySelector(".icon")?.cloneNode(true);
            customButtonIcon?.classList.remove(...allVariantTxtClasses, "dropdown-item-icon");
            modalButton.querySelector(".icon").replaceWith(customButtonIcon || defaultButtonIcon.cloneNode(true));

            // apply to the modal button the same variant as the action that opened the modal
            const variant = actionElement.getAttribute("data-action-variant") || "danger";
            modalButton.classList.remove(...allVariantClasses);
            modalButton.classList.add(variantToClass[variant] || "btn-danger");

            // apply to the modal icon and status message the same variant as the action that opened the modal
            modalStatus.classList.remove(...allVariantBgClasses);
            modalStatus.classList.add(variantBgToClass[variant] || "bg-danger");
            modalIcon.classList.remove(...allVariantTxtClasses);
            modalIcon.classList.add(variantTxtToClass[variant] || "text");

            modalButton.addEventListener("click", () => {
                // Case 1: POST action with formaction (like DELETE with CSRF token)
                const formAction = sanitizeUrl(actionElement.getAttribute("formaction"));
                if (formAction) {
                    const form = document.querySelector("#action-confirmation-form");
                    form?.setAttribute("action", formAction);
                    form?.submit();
                    return;
                }

                // Case 2: dropdown action rendered as form (data-ea-action-form-id)
                const actionFormId = actionElement.getAttribute("data-ea-action-form-id");
                if (actionFormId) {
                    document.getElementById(actionFormId)?.submit();
                    return;
                }

                // Case 3: standalone button inside a <form> (renderAsForm)
                const parentForm = actionElement.closest("form");
                if (parentForm?.hasAttribute("action")) {
                    parentForm.submit();
                    return;
                }

                // Case 4: GET action with href
                const href = sanitizeUrl(actionElement.getAttribute("href"));
                if (href) {
                    window.location.href = href;
                }
            }, { once: true });
        });
    });
}

function createDefaultRowAction() {
    const clickableRows = document.querySelectorAll('tr[data-default-action-url]');
    if (0 === clickableRows.length) {
        return;
    }

    const clickTrigger = clickableRows[0].closest("table")?.getAttribute("data-default-action-trigger") || "single";

    const interactiveSelectors = [
        "a",
        "button",
        "input",
        "select",
        "textarea",
        ".form-check",
        ".dropdown",
        ".actions",
        "[data-bs-toggle]",
        ".btn",
        ".modal",
    ];

    const isInteractiveElement = (element) => {
        // walk up the DOM tree to check if any ancestor is interactive
        // this also handles elements with pointer-events: none whose clicks bubble to parents
        let current = element;
        while (current && current !== document.body) {
            if (interactiveSelectors.some((selector) => current.matches(selector))) {
                return true;
            }
            current = current.parentElement;
        }

        return false;
    };

    const userIsSelectingTextInRow = (row) => {
        if ('double' === clickTrigger) {
            return false;
        }

        const selection = window.getSelection();
        if (null === selection || 0 === selection.toString().length || 0 === selection.rangeCount) {
            return false;
        }

        return row.contains(selection.getRangeAt(0).commonAncestorContainer);
    };

    const navigateToUrl = (url) => {
        // create a temporary link and click it to let Turbo (or other libraries) intercept the navigation
        const link = document.createElement("a");
        link.href = url;
        link.style.display = "none";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    const handleRowActivation = (row) => {
        // don't navigate if rows are selected (batch mode)
        if (row.classList.contains("selected-row")) {
            return;
        }

        const url = sanitizeUrl(row.dataset.defaultActionUrl);
        if (url) {
            navigateToUrl(url);
        }
    };

    clickableRows.forEach((row) => {
        // handle mouse clicks
        row.addEventListener(clickTrigger === "double" ? "dblclick" : "click", (event) => {
            if (isInteractiveElement(event.target)) {
                return;
            }

            if (userIsSelectingTextInRow(row)) {
                return;
            }

            handleRowActivation(row);
        });

        // handle keyboard navigation (Enter and Space)
        row.addEventListener("keydown", (event) => {
            if ("Enter" !== event.key && " " !== event.key) {
                return;
            }

            // don't activate if focus is on an interactive child element
            if (isInteractiveElement(event.target) && event.target !== row) {
                return;
            }

            event.preventDefault();
            handleRowActivation(row);
        });
    });
}

function createActionHandlers() {
    // handle form submissions via data attribute (replaces inline onclick handlers)
    // skip elements with confirmation modals (handled by createActionConfirmationModals)
    document.querySelectorAll("[data-ea-action-form-id]").forEach((element) => {
        element.addEventListener("click", (event) => {
            if (element.hasAttribute("data-action-confirmation")) {
                return;
            }
            event.preventDefault();
            const formId = element.getAttribute("data-ea-action-form-id");
            const targetForm = formId ? document.getElementById(formId) : null;
            targetForm?.submit();
        });
    });

    // handle navigation via data attribute (replaces inline onclick handlers)
    document.querySelectorAll("[data-ea-action-url]").forEach((element) => {
        element.addEventListener("click", (event) => {
            if (element.hasAttribute("data-action-confirmation")) {
                return;
            }
            event.preventDefault();
            const actionUrl = sanitizeUrl(element.getAttribute("data-ea-action-url"));
            if (actionUrl) {
                window.location.href = actionUrl;
            }
        });
    });
}

const Admin = {
    toggleVisibilityClasses,
    createFilterToggles,
    removeHashFromUrl,
    createSearchHighlight,
    createFilters,
    createBatchActions,
    createActionConfirmationModals,
    createDefaultRowAction,
    createActionHandlers,
};

export default Admin;
