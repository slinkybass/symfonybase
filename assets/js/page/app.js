import * as bootstrap from "@tabler/core";
import basicLightbox from "basiclightbox";
import DirtyForm from "dirty-form";
import moment from "moment/min/moment-with-locales.min.js";
import Autocomplete from "../fields/autocomplete.js";
import { trans } from "../../translator.js";

let isNavigatingHistory = false;

const INPUT_ERROR_HANDLER = "__appInvalidInputHandler";

function queryFieldByName(form, fieldName) {
    if (!fieldName) {
        return null;
    }

    return form.querySelector(`[name="${CSS.escape(fieldName)}"]`);
}

function queryRadiosByName(form, radioName) {
    if (!radioName) {
        return [];
    }

    return [...form.querySelectorAll(`input[name="${CSS.escape(radioName)}"]`)];
}

function getSubmitButtonsForForm(form) {
    const inner = [...form.querySelectorAll('[type="submit"]')];
    if (!form.id) {
        return inner;
    }

    return [...inner, ...document.querySelectorAll(`[type="submit"][form="${CSS.escape(form.id)}"]`)];
}

function setMomentLocale() {
    moment.locale(document.querySelector("html")?.getAttribute("lang") ?? "en");
}

function createAutoCompleteFields() {
    const autocomplete = new Autocomplete();
    document.querySelectorAll('[data-ea-widget="ea-autocomplete"], [data-autocomplete-field="true"]').forEach((autocompleteElement) => {
        autocomplete.create(autocompleteElement);
    });
}

function persistSelectedTab() {
    // the ID of the selected tab is appended as a hash in the URL to persist it;
    // if the URL has a hash, try to look for a tab with that ID and show it
    const urlHash = window.location.hash;
    if (urlHash) {
        const selectedTabPaneId = urlHash.substring(1); // remove the leading '#' from the hash
        const selectedTabId = `tablist-${selectedTabPaneId}`;
        setTabAsActive(selectedTabId);
    }

    // update the page anchor when the selected tab changes
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach((tabElement) => {
        tabElement.addEventListener("shown.bs.tab", (event) => {
            // don't push state when navigating through browser history (back/forward)
            if (isNavigatingHistory) {
                return;
            }
            const rawHref = event.target.getAttribute("href");
            if (!rawHref || !rawHref.startsWith("#")) {
                return;
            }
            const newHash = `#${rawHref.substring(1)}`;
            history.pushState({}, "", newHash);
        });
    });

    // handle browser back/forward navigation to restore the correct tab
    window.addEventListener("popstate", () => {
        isNavigatingHistory = true;
        const hash = window.location.hash;
        if (hash) {
            const selectedTabPaneId = hash.substring(1);
            const selectedTabId = `tablist-${selectedTabPaneId}`;
            setTabAsActive(selectedTabId);
        } else {
            const firstTab = document.querySelector('a[data-bs-toggle="tab"]');
            if (firstTab) {
                setTabAsActive(firstTab.id);
            }
        }
        isNavigatingHistory = false;
    });
}

function createUnsavedFormChangesWarning() {
    [".ea-new-form", ".ea-edit-form"].forEach((formSelector) => {
        const form = document.querySelector(formSelector);
        if (!form) {
            return;
        }

        const dirtyForm = new DirtyForm(form, {
            onDirty: () => {
                getSubmitButtonsForForm(form).forEach(startShake);
            },
        });

        form.addEventListener("submit", () => {
            dirtyForm.disconnect();
            getSubmitButtonsForForm(form).forEach(stopShake);
        });
    });

    function startShake(button) {
        if (button.dataset.shaking === "true") {
            return;
        }
        button.dataset.shaking = "true";
        function loop() {
            if (button.dataset.shaking !== "true") {
                return;
            }
            button.style.animation = "pulse 0.9s";
            setTimeout(() => {
                button.style.animation = "";
                setTimeout(loop, 5000);
            }, 900);
        }
        loop();
    }

    function stopShake(button) {
        button.dataset.shaking = "false";
        button.style.animation = "";
    }
}

function createFieldsWithErrors() {
    document.querySelectorAll("form").forEach((form) => {
        getSubmitButtonsForForm(form).forEach((button) => {
            button.addEventListener("click", function onSubmitButtonsClick(clickEvent) {
                let formHasErrors = false;
                if (null !== form.getAttribute("novalidate")) {
                    return;
                }

                let firstInputError = null;
                let firstTabError = null;
                form.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach((input) => {
                    input.classList.remove("is-invalid");
                    let errorDiv = input.parentNode.querySelector(".invalid-feedback");
                    if (errorDiv) {
                        errorDiv.remove();
                    }

                    const name = input.name;
                    const isRepeatedFirstField = name && name.endsWith("[first]");
                    if (isRepeatedFirstField) {
                        const secondElementName = name.replace("[first]", "[second]");
                        const secondElement = queryFieldByName(form, secondElementName);
                        if (secondElement) {
                            if (secondElement.value !== input.value) {
                                const validityMessage = trans("The values do not match.", {}, "validators");
                                input.setCustomValidity(validityMessage);
                                secondElement.setCustomValidity(validityMessage);
                            } else {
                                input.setCustomValidity("");
                                secondElement.setCustomValidity("");
                            }
                        }
                    }

                    if (!input.disabled && !input.validity.valid) {
                        formHasErrors = true;
                        const errorMessage = input.validationMessage;
                        if (!firstInputError) {
                            firstInputError = input;
                        }

                        input.classList.add("is-invalid");
                        if (errorMessage) {
                            errorDiv = document.createElement("div");
                            errorDiv.className = "invalid-feedback";
                            errorDiv.textContent = errorMessage;
                            input.parentNode.appendChild(errorDiv);
                        }

                        // Check if the input is inside of a tab-pane
                        const tabPane = input.closest(".tab-pane");
                        if (tabPane) {
                            const tabHref = `#${tabPane.id}`;
                            const tab = document.querySelector(`[data-bs-toggle="tab"][href="${CSS.escape(tabHref)}"]`);
                            if (tab) {
                                if (!firstTabError) {
                                    firstTabError = tab;
                                }
                                if (firstTabError !== tab && !tab.classList.contains("text-danger")) {
                                    tab.classList.add("text-danger");

                                    tab.addEventListener("click", function onTabClick() {
                                        tab.classList.remove("text-danger");
                                    });
                                }
                            }
                        }

                        const existingHandler = input[INPUT_ERROR_HANDLER];
                        if (existingHandler) {
                            input.removeEventListener("input", existingHandler);
                            input.removeEventListener("change", existingHandler);
                        }

                        function handleInputEvent() {
                            input.classList.remove("is-invalid");
                            const errorDivInner = input.parentNode.querySelector(".invalid-feedback");
                            if (errorDivInner) {
                                errorDivInner.remove();
                            }

                            const inputName = input.name;
                            const isRepeatedFirst = inputName && inputName.endsWith("[first]");
                            if (isRepeatedFirst) {
                                const secondElementNameInner = inputName.replace("[first]", "[second]");
                                const secondElementInner = queryFieldByName(form, secondElementNameInner);
                                if (secondElementInner) {
                                    secondElementInner.classList.remove("is-invalid");
                                    const errorDivSecond = secondElementInner.parentNode.querySelector(".invalid-feedback");
                                    if (errorDivSecond) {
                                        errorDivSecond.remove();
                                    }
                                }
                            }
                            const isRepeatedSecondField = inputName && inputName.endsWith("[second]");
                            if (isRepeatedSecondField) {
                                const firstElementNameInner = inputName.replace("[second]", "[first]");
                                const firstElementInner = queryFieldByName(form, firstElementNameInner);
                                if (firstElementInner) {
                                    firstElementInner.classList.remove("is-invalid");
                                    const errorDivFirst = firstElementInner.parentNode.querySelector(".invalid-feedback");
                                    if (errorDivFirst) {
                                        errorDivFirst.remove();
                                    }
                                }
                            }

                            input.removeEventListener("input", handleInputEvent);
                            input.removeEventListener("change", handleInputEvent);
                            delete input[INPUT_ERROR_HANDLER];

                            if (input.type === "radio") {
                                const radioName = input.name;
                                queryRadiosByName(form, radioName).forEach((radio) => {
                                    radio.classList.remove("is-invalid");
                                    const errorDivRadio = radio.parentNode.querySelector(".invalid-feedback");
                                    if (errorDivRadio) {
                                        errorDivRadio.remove();
                                    }
                                    radio.removeEventListener("input", handleInputEvent);
                                    radio.removeEventListener("change", handleInputEvent);
                                    delete radio[INPUT_ERROR_HANDLER];
                                });
                            }
                        }

                        input[INPUT_ERROR_HANDLER] = handleInputEvent;
                        input.addEventListener("input", handleInputEvent);
                        input.addEventListener("change", handleInputEvent);
                    }
                });

                if (formHasErrors) {
                    if (firstTabError) {
                        const Tab = bootstrap.Tab;
                        const bootstrapTab = new Tab(firstTabError);
                        bootstrapTab.show();
                    }
                    if (firstInputError) {
                        firstInputError.focus();
                    }
                    clickEvent.preventDefault();
                    clickEvent.stopPropagation();
                }
            });
        });
    });
}

function createLightboxes() {
    document.querySelectorAll('[data-action="zoom"]').forEach((link) => {
        link.addEventListener("click", (e) => {
            e.preventDefault();
            const href = link.getAttribute("href");
            const img = document.createElement("img");
            img.width = 1400;
            img.height = 900;
            img.src = href ?? "";
            basicLightbox.create(img).show();
        });
    });
}

function setTabAsActive(tabItemId) {
    const tabElement = document.getElementById(tabItemId);
    if (!tabElement) {
        return;
    }

    const Tab = bootstrap.Tab;
    const bootstrapTab = new Tab(tabElement);
    bootstrapTab.show();
}

function preventMultipleFormSubmission() {
    document.querySelectorAll("form").forEach((form) => {
        form.addEventListener(
            "submit",
            () => {
                // this timeout is needed to include the disabled button into the submitted form
                setTimeout(() => {
                    getSubmitButtonsForForm(form).forEach((button) => {
                        button.classList.add("btn-loading");
                    });
                }, 1);
            },
            false
        );
    });
}

function relocateTableModals() {
    document.querySelectorAll("tr .modal").forEach((modal) => {
        document.body.appendChild(modal);
    });
}

const App = {
    setMomentLocale,
    createAutoCompleteFields,
    persistSelectedTab,
    createUnsavedFormChangesWarning,
    createFieldsWithErrors,
    createLightboxes,
    setTabAsActive,
    preventMultipleFormSubmission,
    relocateTableModals,
};

export default App;
