/**
 * Hierarchical Field Visibility Handler
 *
 * Author: slinkybass
 * Version: 3.6
 *
 * Description:
 * Manages form field visibility and behavior based on hierarchical parent-child relationships,
 * using declarative `data-hf-*` attributes.
 *
 * Events:
 * - change (all controls) / input (text-like controls): value change of a parent field.
 * - DOMContentLoaded (or immediately if the DOM is already parsed): initializes listeners, and again on the
 *   next tick so TomSelect/Flatpickr instances already exist.
 * - ea.collection.item-added / ea.collection.item-removed: re-evaluates hierarchy for EasyAdmin collections.
 *
 * Supported `data-hf-*` Attributes:
 * - data-hf-parent: Parent input; identifies a group name.
 * - data-hf-child: Child input; matches a parent group name.
 * - data-hf-scope: Optional, on any ancestor element. Limits the lookup of children to that element
 *   (e.g. one collection row). Nearest SCOPE_SELECTOR ancestor wins; if it has no children of the group,
 *   the lookup climbs to the next outer scope and finally to the whole document.
 * - data-hf-container: Optional. Selector of the container to hide. If several children share a container,
 *   it is hidden only when ALL of them are hidden.
 * - data-hf-default-value: Optional. Default value to restore when shown (kept as a raw string for text).
 * - data-hf-parent-value: Optional. Value (or values separated by `|`, or a JSON array) required from the parent.
 * - data-hf-match: Optional. How the parent's current values (P) are compared with the values the child asks for
 *   in data-hf-parent-value (C):
 *   "any" (default): P has at least one value of C.
 *   "all": P contains every value of C (P may have more).
 *   "exact": P is the same set as C.
 *   "subset": P is not empty and every value in P belongs to C.
 *   Without data-hf-parent-value the child is shown when the parent has a value (match is ignored).
 * - data-hf-negate: Optional. Presence or "true" inverts the result (an empty parent then shows the child).
 * - data-hf-keep-value: Optional. Presence (or any value except "false"/"0") prevents clearing when hiding.
 * - data-hf-save-value: Optional. Presence (or any value except "false"/"0") stores the value when hidden and restores it when shown.
 * - data-hf-saved-value: Internal. Stored hidden value.
 * - data-hf-show: Optional. Presence (or any value except "false"/"0") keeps the field visible but locked instead of hiding it.
 *   On hide it still clears the value unless keep-value/save-value is set (same as 3.4).
 * - data-hf-required: Internal. Restores the required state when the field is shown again.
 * - data-hf-state / data-hf-evaluated: Internal.
 *
 * Compatibility:
 * - Native inputs: checkbox, radio, select, text, etc.
 * - TomSelect (`.tomselected` + `tomselect` instance), including lock in `data-hf-show` mode.
 * - Flatpickr (`.flatpickr-input` + `_flatpickr` instance), including altInput.
 */

(function () {
    // Nearest ancestors that delimit a group of related fields. Adjust the row class to your collection theme.
    const SCOPE_SELECTOR = "[data-hf-scope], .field-collection-item, .collection-item, form";

    const NON_TEXT_INPUTS = ["checkbox", "radio", "file", "button", "submit", "reset", "image", "hidden", "range", "color"];

    const scheduleInit = () => {
        initHierarchyFields();
        // Widget plugins initialize TomSelect/Flatpickr in the same turn as these events.
        // A second pass sees those instances.
        setTimeout(initHierarchyFields, 0);
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", scheduleInit);
    } else {
        scheduleInit();
    }
    document.addEventListener("ea.collection.item-added", scheduleInit);
    document.addEventListener("ea.collection.item-removed", scheduleInit);

    /**
     * EasyAdmin puts data-hf-* on the field wrapper DIV on detail pages. Only real controls drive hierarchy.
     *
     * @param {Element} el
     * @returns {boolean}
     */
    function isFormControl(el) {
        return !!el && ["INPUT", "SELECT", "TEXTAREA"].includes(el.tagName);
    }

    /**
     * Binds parent fields once, then evaluates every parent/child group.
     * Safe to call again: value changes only run on a visibility transition.
     */
    function initHierarchyFields() {
        document.querySelectorAll("[data-hf-parent]").forEach((parent) => {
            if (!isFormControl(parent)) {
                return;
            }
            const groupName = parent.dataset.hfParent ?? "";
            const evaluate = () => {
                const children = findChildren(parent, groupName);
                if (children.length) {
                    handleHierarchy(parent, children);
                }
            };

            if (parent.dataset.hfInitialized === undefined) {
                parent.dataset.hfInitialized = "";
                // Non text-like controls only emit meaningful `change`; listening to `input` too just doubles the work.
                const events = isTextLike(parent) ? ["input", "change"] : ["change"];
                events.forEach((name) => parent.addEventListener(name, evaluate));
            }
            evaluate();
        });
    }

    /**
     * Finds the children of a group, preferring the nearest scope (e.g. the same collection row).
     *
     * @param {HTMLElement} parent
     * @param {string} groupName
     * @returns {HTMLElement[]}
     */
    function findChildren(parent, groupName) {
        const selector = `[data-hf-child="${escapeAttr(groupName)}"]`;
        let scope = parent.closest(SCOPE_SELECTOR);
        while (scope) {
            const found = Array.from(scope.querySelectorAll(selector)).filter(isFormControl);
            if (found.length) {
                return found;
            }
            scope = scope.parentElement ? scope.parentElement.closest(SCOPE_SELECTOR) : null;
        }

        return Array.from(document.querySelectorAll(selector)).filter(isFormControl);
    }

    /**
     * @param {HTMLElement} parent
     * @param {HTMLElement[]} children
     */
    function handleHierarchy(parent, children) {
        const parentType = parent.type;
        const parentValue = readParentValue(parent);
        const cards = new Set();

        children.forEach((child) => {
            processChild(parentValue, parentType, child);
            const card = child.closest(".card");
            if (card) {
                cards.add(card);
            }
        });

        // One pass per card instead of one per child.
        cards.forEach(updateCardVisibility);
    }

    /**
     * @param {HTMLElement} parent
     * @returns {*|null}
     */
    function readParentValue(parent) {
        switch (parent.type) {
            case "checkbox":
                return parent.checked;
            case "radio": {
                const checked = document.querySelector(`[name="${escapeAttr(parent.name)}"]:checked`);
                return checked ? parseFieldValue(checked.value) : null;
            }
            case "select-multiple":
                return Array.from(parent.selectedOptions).map((option) => parseFieldValue(option.value));
            default:
                return parseFieldValue(parent.value);
        }
    }

    /**
     * @param {*} parentValue
     * @param {string} parentType
     * @param {HTMLElement} child
     */
    function processChild(parentValue, parentType, child) {
        const container = resolveContainer(child);
        if (!container) {
            return;
        }

        const childType = child.type;
        const ctx = {
            isCheckbox: childType === "checkbox" || childType === "radio",
            isTomSelect: child.classList.contains("tomselected") && !!child.tomselect,
            isFlatpickr: child.classList.contains("flatpickr-input") && !!child._flatpickr,
        };

        const keepValue = readFlag(child, "data-hf-keep-value");
        const saveValue = readFlag(child, "data-hf-save-value");
        const forceShow = readFlag(child, "data-hf-show");
        const shouldShow = parentMatches(parentValue, parentType, child);
        const wasShown = isChildShown(child, container, forceShow);
        const firstEvaluation = child.dataset.hfEvaluated === undefined;

        if (shouldShow) {
            child.dataset.hfState = "shown";
            showChild(child, container, forceShow);
            // First paint may fill an empty text/select. Checkboxes keep the server state until a real hide → show.
            if (!wasShown || (firstEvaluation && !ctx.isCheckbox)) {
                restoreValue(child, ctx, keepValue, saveValue);
                restoreRequired(child, ctx.isFlatpickr);
            }
            child.dataset.hfEvaluated = "";
        } else if (wasShown) {
            saveAndClearValue(child, ctx, keepValue, saveValue);
            storeRequired(child, ctx.isFlatpickr);
            child.dataset.hfState = "hidden";
            hideChild(child, container, forceShow);
            child.dataset.hfEvaluated = "";
        } else {
            // Already hidden: re-apply visibility/lock (idempotent) so widgets created later get locked too.
            child.dataset.hfState = "hidden";
            hideChild(child, container, forceShow);
        }
    }

    /**
     * `|` separates alternative parent values (OR). A JSON array is accepted too.
     * `data-hf-match` (any|all|exact|subset) picks the set relation and `data-hf-negate` inverts it.
     *
     * @param {*} parentValue
     * @param {string} parentType
     * @param {HTMLElement} child
     * @returns {boolean}
     */
    function parentMatches(parentValue, parentType, child) {
        const raw = child.dataset.hfParentValue;
        let childValues = [];
        if (raw !== undefined && raw !== "") {
            const parsed = parseFieldValue(raw);
            const parts = Array.isArray(parsed) ? parsed : String(parsed).split("|");
            childValues = parts.map((value) => parseFieldValue(value));
        } else if (parentType === "checkbox") {
            childValues = [true];
        }

        if (parentType === "checkbox") {
            // A checkbox parent is boolean: accept 1/"true" and 0/"false" as written in data-hf-parent-value.
            childValues = childValues.map(toBoolean);
        }

        const hasValue = Array.isArray(parentValue) ? parentValue.length > 0 : parentValue !== null && parentValue !== undefined;
        const negate = readFlag(child, "data-hf-negate");
        if (!childValues.length) {
            return negate ? !hasValue : hasValue;
        }

        // P = current parent values (parentValues), C = values the child asks for (childValues).
        const parentValues = hasValue ? (Array.isArray(parentValue) ? parentValue : [parentValue]) : [];
        const inParent = (candidate) => parentValues.some((value) => valuesEqual(value, candidate));
        const inChild = (value) => childValues.some((candidate) => valuesEqual(value, candidate));
        const mode = (child.dataset.hfMatch ?? "any").toLowerCase();

        let result;
        switch (mode) {
            case "all":
                result = childValues.every(inParent);
                break;
            case "exact":
                result = childValues.every(inParent) && parentValues.every(inChild);
                break;
            case "subset":
                result = parentValues.length > 0 && parentValues.every(inChild);
                break;
            default:
                result = childValues.some(inParent);
        }

        return negate ? !result : result;
    }

    /**
     * @param {HTMLElement} child
     * @returns {HTMLElement|null}
     */
    function resolveContainer(child) {
        if (child.dataset.hfContainer) {
            try {
                const custom = child.closest(child.dataset.hfContainer);
                if (custom) {
                    return custom;
                }
            } catch {
                // Invalid selector: fall back to the EasyAdmin field wrapper.
            }
        }

        return child.closest(".form-group")?.parentElement ?? child.parentElement ?? null;
    }

    /**
     * Uses the state this script stored; falls back to the DOM on the very first evaluation.
     *
     * @param {HTMLElement} child
     * @param {HTMLElement} container
     * @param {boolean} forceShow
     * @returns {boolean}
     */
    function isChildShown(child, container, forceShow) {
        const state = child.dataset.hfState;
        if (state !== undefined) {
            return state === "shown";
        }

        return forceShow ? !child.classList.contains("hf-locked") : !container.classList.contains("d-none");
    }

    /**
     * @param {HTMLElement} child
     * @param {HTMLElement} container
     * @param {boolean} forceShow
     */
    function showChild(child, container, forceShow) {
        if (forceShow) {
            setLocked(child, false);
        } else {
            container.classList.remove("d-none");
        }
    }

    /**
     * A shared container is hidden only when no other child inside it is (or may still be) shown.
     *
     * @param {HTMLElement} child
     * @param {HTMLElement} container
     * @param {boolean} forceShow
     */
    function hideChild(child, container, forceShow) {
        if (forceShow) {
            setLocked(child, true);
            return;
        }

        const othersShown = Array.from(container.querySelectorAll("[data-hf-child]")).some(
            (el) => el !== child && !readFlag(el, "data-hf-show") && el.dataset.hfState !== "hidden"
        );
        if (!othersShown) {
            container.classList.add("d-none");
        }
    }

    /**
     * @param {HTMLElement} el
     * @returns {boolean}
     */
    function isTextLike(el) {
        return el.tagName === "TEXTAREA" || (el.tagName === "INPUT" && !NON_TEXT_INPUTS.includes(el.type));
    }

    /**
     * Text-like controls use `readonly` (value still submits). Selects, checkboxes and radios ignore
     * `readonly`, so they are locked visually and ignore edits without using `disabled`.
     * TomSelect and Flatpickr widgets are locked through their own APIs. Idempotent.
     *
     * @param {HTMLElement} child
     * @param {boolean} locked
     */
    function setLocked(child, locked) {
        child.classList.toggle("disabled", locked);
        child.classList.toggle("hf-locked", locked);

        if (child.dataset.hfLockBound === undefined) {
            child.dataset.hfLockBound = "";
            const blockLockedEdit = (event) => {
                if (child.classList.contains("hf-locked") && child.getAttribute("readonly") === null) {
                    event.preventDefault();
                }
            };
            child.addEventListener("click", blockLockedEdit);
            child.addEventListener("keydown", blockLockedEdit);
        }

        if (child.tomselect) {
            if (locked) {
                child.tomselect.lock();
            } else {
                child.tomselect.unlock();
            }
        }

        if (child._flatpickr) {
            child._flatpickr.set("clickOpens", !locked);
            const alt = child._flatpickr.altInput;
            if (alt) {
                if (locked && !alt.hasAttribute("readonly")) {
                    alt.setAttribute("readonly", "readonly");
                    alt.dataset.hfLocked = "";
                } else if (!locked && alt.dataset.hfLocked !== undefined) {
                    alt.removeAttribute("readonly");
                    delete alt.dataset.hfLocked;
                }
            }
        }

        if (isTextLike(child)) {
            if (locked) {
                child.setAttribute("readonly", "readonly");
            } else {
                child.removeAttribute("readonly");
            }
            return;
        }

        child.classList.toggle("pe-none", locked);
        if (locked) {
            child.setAttribute("aria-readonly", "true");
            child.setAttribute("tabindex", "-1");
        } else {
            child.removeAttribute("aria-readonly");
            child.removeAttribute("tabindex");
        }
    }

    /**
     * Text values (default and saved) are handled as raw strings so "1e3" or "0.50" are never altered.
     *
     * @param {HTMLElement} child
     * @param {{isCheckbox: boolean, isTomSelect: boolean, isFlatpickr: boolean}} ctx
     * @param {boolean} keep
     * @param {boolean} save
     */
    function restoreValue(child, ctx, keep, save) {
        if (child.disabled || keep) {
            return;
        }

        const before = snapshotValue(child, ctx);
        const rawDefault = child.dataset.hfDefaultValue;
        const hasDefault = rawDefault !== undefined && rawDefault !== "";
        const rawSaved = child.dataset.hfSavedValue;
        const hasSaved = save && rawSaved !== undefined;

        if (ctx.isCheckbox) {
            if (hasSaved) {
                child.checked = rawSaved === "true";
            } else if (hasDefault) {
                child.checked = isTruthy(rawDefault);
            }
        } else if (ctx.isTomSelect) {
            const current = child.tomselect.getValue();
            const empty = Array.isArray(current) ? current.length === 0 : current === "" || current === null;
            if (hasSaved) {
                child.tomselect.setValue(safeJson(rawSaved), true);
            } else if (hasDefault && empty) {
                child.tomselect.setValue(parseList(rawDefault, child.multiple), true);
            }
        } else if (ctx.isFlatpickr) {
            if (hasSaved) {
                child._flatpickr.setDate(rawSaved, false);
            } else if (hasDefault && !child.value) {
                child._flatpickr.setDate(rawDefault, false);
            }
        } else if (hasSaved) {
            child.value = rawSaved;
        } else if (hasDefault && !child.value) {
            child.value = rawDefault;
        }

        child.removeAttribute("data-hf-saved-value");
        if (snapshotValue(child, ctx) !== before) {
            emitValueEvent(child);
        }
    }

    /**
     * @param {HTMLElement} child
     * @param {{isCheckbox: boolean, isTomSelect: boolean, isFlatpickr: boolean}} ctx
     * @param {boolean} keep
     * @param {boolean} save
     */
    function saveAndClearValue(child, ctx, keep, save) {
        if (child.disabled || keep) {
            return;
        }

        const before = snapshotValue(child, ctx);
        if (ctx.isCheckbox) {
            if (save) {
                child.dataset.hfSavedValue = child.checked ? "true" : "false";
            }
            child.checked = false;
        } else if (ctx.isTomSelect) {
            const val = child.tomselect.getValue();
            if (save && (Array.isArray(val) ? val.length : val)) {
                child.dataset.hfSavedValue = JSON.stringify(val);
            }
            child.tomselect.clear(true);
        } else if (ctx.isFlatpickr) {
            if (save && child.value) {
                child.dataset.hfSavedValue = child.value;
            }
            child._flatpickr.clear(false);
        } else {
            if (save && child.value) {
                child.dataset.hfSavedValue = child.value;
            }
            child.value = "";
        }

        if (snapshotValue(child, ctx) !== before) {
            emitValueEvent(child);
        }
    }

    /**
     * @param {HTMLElement} child
     * @param {{isCheckbox: boolean, isTomSelect: boolean}} ctx
     * @returns {string}
     */
    function snapshotValue(child, ctx) {
        if (ctx.isCheckbox) {
            return child.checked ? "1" : "0";
        }
        if (ctx.isTomSelect) {
            return JSON.stringify(child.tomselect.getValue());
        }

        return child.value ?? "";
    }

    /**
     * Cascades to children that are themselves parents. Only called when the value really changed,
     * so cyclic hierarchies cannot loop. The events do not bubble.
     *
     * @param {HTMLElement} child
     */
    function emitValueEvent(child) {
        child.dispatchEvent(new Event("input"));
        child.dispatchEvent(new Event("change"));
    }

    /**
     * @param {HTMLElement} child
     * @param {boolean} isFlatpickr
     */
    function restoreRequired(child, isFlatpickr) {
        if (!child.hasAttribute("data-hf-required")) {
            return;
        }

        fieldLabels(child).forEach((label) => {
            label.classList.remove("hf-required");
            label.classList.add("required");
        });
        child.setAttribute("required", child.dataset.hfRequired ?? "");
        child.removeAttribute("data-hf-required");

        const alt = flatpickrAltInput(child, isFlatpickr);
        if (alt?.hasAttribute("data-hf-required")) {
            alt.setAttribute("required", alt.dataset.hfRequired ?? "");
            alt.removeAttribute("data-hf-required");
        }
    }

    /**
     * @param {HTMLElement} child
     * @param {boolean} isFlatpickr
     */
    function storeRequired(child, isFlatpickr) {
        if (child.hasAttribute("required")) {
            fieldLabels(child).forEach((label) => {
                label.classList.remove("required");
                label.classList.add("hf-required");
            });
            child.dataset.hfRequired = child.getAttribute("required") ?? "";
            child.removeAttribute("required");
        }

        const alt = flatpickrAltInput(child, isFlatpickr);
        if (alt?.hasAttribute("required")) {
            alt.dataset.hfRequired = alt.getAttribute("required") ?? "";
            alt.removeAttribute("required");
        }
    }

    /**
     * Labels of this field only, so a shared hide-container cannot strip `required` from siblings.
     *
     * @param {HTMLElement} child
     * @returns {HTMLElement[]}
     */
    function fieldLabels(child) {
        const scope = child.closest(".form-group") ?? child.parentElement;
        if (!scope) {
            return [];
        }

        return Array.from(scope.querySelectorAll("label.required, label.hf-required"));
    }

    /**
     * @param {HTMLElement} child
     * @param {boolean} isFlatpickr
     * @returns {HTMLElement|null}
     */
    function flatpickrAltInput(child, isFlatpickr) {
        if (!isFlatpickr) {
            return null;
        }

        const input = child._flatpickr.altInput || child._flatpickr._input;
        if (!input || input === child) {
            return null;
        }

        return input;
    }

    /**
     * Hides a card that only contains hierarchy fields when every one of them is hidden.
     * TomSelect and Flatpickr helper inputs belong to the child field, so they do not keep the card open.
     *
     * @param {HTMLElement} card
     */
    function updateCardVisibility(card) {
        const childs = Array.from(card.querySelectorAll("[data-hf-child]")).filter(isFormControl);
        // Detail pages put data-hf-* on wrappers with no form controls — leave those cards alone.
        if (!childs.length) {
            return;
        }

        const visible = childs.some((ch) => {
            if (readFlag(ch, "data-hf-show")) {
                return !ch.classList.contains("hf-locked");
            }
            const container = resolveContainer(ch);
            return container ? !container.classList.contains("d-none") : true;
        });

        const otherFields = Array.from(card.querySelectorAll("input, select, textarea")).filter((el) => {
            if (el.type === "hidden" || el.dataset.hfChild) {
                return false;
            }
            if (el.closest(".ts-wrapper, .flatpickr-calendar")) {
                return false;
            }
            const group = el.closest(".form-group");
            return !group?.querySelector("[data-hf-child]");
        });

        card.classList.toggle("d-none", !visible && otherFields.length === 0);
    }

    /**
     * Boolean attributes: present with any value counts as true, except explicit "false" / "0"
     * (and their JSON forms). Matches 3.4 tolerance for Symfony rendering `attr="attr"` when
     * PHP passes `true`.
     *
     * @param {HTMLElement} el
     * @param {string} attr
     * @returns {boolean}
     */
    function readFlag(el, attr) {
        if (!el.hasAttribute(attr)) {
            return false;
        }

        return isTruthy(el.getAttribute(attr));
    }

    /**
     * Strict comparison: a boolean only equals a boolean, everything else compares as a string.
     * Avoids the `==` traps ("" == 0, 0 == false, 1 == true). Checkbox parents are normalized
     * to booleans in parentMatches, so `data-hf-parent-value="1"` still works for them.
     *
     * @param {*} left
     * @param {*} right
     * @returns {boolean}
     */
    function valuesEqual(left, right) {
        if (left === null || right === null || left === undefined || right === undefined) {
            return false;
        }
        if (typeof left === "boolean" || typeof right === "boolean") {
            return left === right;
        }

        return String(left) === String(right);
    }

    /**
     * @param {*} value
     * @returns {*}
     */
    function toBoolean(value) {
        if ([true, 1, "1", "true"].includes(value)) {
            return true;
        }
        if ([false, 0, "0", "false"].includes(value)) {
            return false;
        }

        return value;
    }

    /**
     * Empty / missing string → true (attribute presence). Only "false" and "0" (and JSON false/0) are false.
     *
     * @param {*} raw
     * @returns {boolean}
     */
    function isTruthy(raw) {
        if (raw === "" || raw === null || raw === undefined) {
            return true;
        }
        const parsed = typeof raw === "string" ? parseFieldValue(raw) : raw;
        if (parsed === false || parsed === 0) {
            return false;
        }
        if (typeof parsed === "string" && ["false", "0"].includes(parsed.toLowerCase())) {
            return false;
        }

        return true;
    }

    /**
     * @param {string} raw
     * @param {boolean} multiple
     * @returns {string|string[]}
     */
    function parseList(raw, multiple) {
        const parsed = safeJson(raw);
        if (Array.isArray(parsed)) {
            return parsed.map(String);
        }

        return multiple
            ? String(raw)
                  .split(",")
                  .map((part) => part.trim())
                  .filter(Boolean)
            : String(raw);
    }

    /**
     * @param {string} value
     * @returns {string}
     */
    function escapeAttr(value) {
        return String(value).replace(/\\/g, "\\\\").replace(/"/g, '\\"');
    }

    /**
     * @param {string} raw
     * @returns {*}
     */
    function safeJson(raw) {
        try {
            return JSON.parse(raw);
        } catch {
            return raw;
        }
    }

    /**
     * Parses a field value for COMPARISON only (parent values, data-hf-parent-value, flags).
     * Tries JSON; if that fails, returns the raw value, or null if empty.
     *
     * @param {*} val
     * @returns {*}
     */
    function parseFieldValue(val) {
        if (val === undefined || val === null || val === "") {
            return null;
        }
        if (typeof val !== "string") {
            return val;
        }

        return safeJson(val);
    }
})();