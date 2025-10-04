/**
 * Hierarchical Field Visibility Handler
 *
 * Author: slinkybass
 * Version: 3.1
 *
 * Description:
 * This plugin dynamically manages form field visibility and behavior based on hierarchical parent-child relationships.
 * It uses `data-hf-*` attributes to declaratively define how child fields respond to changes in parent field values.
 *
 * Events:
 * - input: Triggered when the value of a parent field changes.
 * - DOMContentLoaded: Initializes hierarchy listeners on page load.
 * - ea.collection.item-added: Re-initializes hierarchy for dynamically added elements (e.g., EasyAdmin collections).
 *
 * Core Functionality:
 * 1. Detects parent fields and associates their children using data-hf attributes.
 * 2. Controls child field visibility based on the parent value and optional conditions.
 * 3. Toggles and restores `required` attributes dynamically.
 * 4. Resets, saves, or preserves field values based on configuration.
 * 5. Handles complex UI widgets like TomSelect and Flatpickr.
 * 6. Automatically hides or shows parent cards based on children visibility.
 *
 * Supported `data-hf-*` Attributes:
 * - data-hf-parent: Attribute for parent input; identifies a group name.
 * - data-hf-child: Attribute for child input; matches a parent name.
 * - data-hf-default-value: Optional. Default value to restore when shown.
 * - data-hf-parent-value: Optional. Specific value required from parent to show this child.
 * - data-hf-keep-value: Optional. Prevents clearing value when hiding.
 * - data-hf-save-value: Optional. Temporarily stores value when hidden and restores it when shown again.
 * - data-hf-saved-value: Internal. Temporarily stores the hidden value (managed by the script).
 * - data-hf-show: Optional. Show element as readonly instead of hiding it.
 * - data-hf-required: Internal. Used to restore required state when field is shown again.
 *
 * Compatibility:
 * - Works with native inputs: checkbox, radio, select, text, etc.
 * - Supports TomSelect (via .tomselected class and tomselect instance).
 * - Supports Flatpickr (via .flatpickr-input class and _flatpickr instance).
 */

(function () {
	document.addEventListener("DOMContentLoaded", initHierarchyFields);
	document.addEventListener("ea.collection.item-added", initHierarchyFields);

	/**
	 * Initializes all hierarchy field relationships.
	 * Scans for elements with the `data-hf-parent` attribute, then finds and binds input event listeners to control child visibility and behavior.
	 *
	 * Called on DOMContentLoaded and ea.collection.item-added.
	 */
	function initHierarchyFields() {
		document.querySelectorAll("[data-hf-parent]").forEach((parent) => {
			const groupName = parent.dataset.hfParent;
			const childs = document.querySelectorAll(`[data-hf-child="${groupName}"]`);
			if (!childs.length) return;

			const handleHierarchyFields = () => handleHierarchy(parent, childs);
			parent.addEventListener("input", handleHierarchyFields);
			handleHierarchyFields();
		});
	}

	/**
	 * Handles logic for each parent-child relationship.
	 * Checks the parent's value and updates each child's visibility, state, and attributes based on custom data-* attributes.
	 *
	 * @param {HTMLElement} parent - The controlling parent field element.
	 * @param {NodeListOf<HTMLElement>} childs - A list of child field elements related to the parent.
	 */
	function handleHierarchy(parent, childs) {
		const parentType = parent.type;
		const isCheckboxOrRadio = parentType === "checkbox" || parentType === "radio";

		let parentValue;
		switch (parentType) {
			case "checkbox":
				parentValue = parent.checked;
				break;
			case "radio":
				parentValue = document.querySelector(`[name="${parent.name}"]:checked`)?.value;
				break;
			default:
				parentValue = parent.value;
		}
		parentValue = parseFieldValue(parentValue);

		Array.from(childs).forEach((child) => processChild(parentValue, isCheckboxOrRadio, child));
	}

	/**
	 * Applies visibility and state logic to a single child element based on the parent value and child's configuration.
	 *
	 * @param {*} parentValue - The current value of the parent element.
	 * @param {boolean} parentIsCheckboxOrRadio - Whether the parent is a checkbox or radio.
	 * @param {HTMLElement} child - The child element to process.
	 */
	function processChild(parentValue, parentIsCheckboxOrRadio, child) {
		const container = child.closest(".form-group");
		if (!container) return;

		const childType = child.type;
		const isCheckboxOrRadio = childType === "checkbox" || childType === "radio";
		const isTomSelect = child.classList.contains("tomselected") && child.tomselect;
		const isFlatpickr = child.classList.contains("flatpickr-input") && child._flatpickr;

		const defaultValue = parseFieldValue(child.dataset.hfDefaultValue);
		const hfParentValue = parseFieldValue(child.dataset.hfParentValue);
		const hfParentValues = hfParentValue !== null ? hfParentValue.toString().split("|").map((v) => parseFieldValue(v)) : [];
		const valuesToShow = hfParentValues.length ? hfParentValues : parentIsCheckboxOrRadio ? [ true ] : [];
		const keepValue = parseFieldValue(child.dataset.hfKeepValue);
		const saveValue = parseFieldValue(child.dataset.hfSaveValue);
		const savedValue = parseFieldValue(child.dataset.hfSavedValue);
		const forceShow = parseFieldValue(child.dataset.hfShow);

		const shouldShow = (valuesToShow.length && valuesToShow.includes(parentValue)) || (!valuesToShow.length && parentValue !== null);

		if (shouldShow) {
			showChild(child, container, forceShow);
			restoreValue(child, savedValue, defaultValue, isCheckboxOrRadio, isTomSelect, isFlatpickr, keepValue, saveValue);
			restoreRequired(child, container, isFlatpickr);
		} else {
			hideChild(child, container, forceShow);
			saveAndClearValue(child, isCheckboxOrRadio, isTomSelect, isFlatpickr, keepValue, saveValue);
			storeRequired(child, container, isFlatpickr);
		}

		updateCardVisibility(child);
	}

	/**
	 * Shows a child element and restores its value/required attributes if necessary.
	 *
	 * @param {HTMLElement} child - The child element to show.
	 * @param {HTMLElement} container - The container element around the child (usually .form-group parent).
	 * @param {boolean} forceShow - If true, disables hiding and just sets the field as readonly instead.
	 */
	function showChild(child, container, forceShow) {
		if (forceShow) {
			child.classList.remove("disabled");
			child.removeAttribute("readonly");
		} else {
			container.classList.remove("d-none");
		}
	}

	/**
	 * Hides a child element and optionally stores and clears its value.
	 *
	 * @param {HTMLElement} child - The child element to hide.
	 * @param {HTMLElement} container - The container element around the child (usually .form-group parent).
	 * @param {boolean} forceShow - If true, disables hiding and only sets the field as readonly instead.
	 */
	function hideChild(child, container, forceShow) {
		if (forceShow) {
			child.classList.add("disabled");
			child.setAttribute("readonly", true);
		} else {
			container.classList.add("d-none");
		}
	}

	/**
	 * Restores the value of a child element, handling various input types like text, checkbox, TomSelect, or Flatpickr.
	 *
	 * @param {HTMLElement} child - The child element to restore.
	 * @param {*} saved - Previously saved value (from data-hf-saved-value).
	 * @param {*} defVal - Default value (from data-hf-default-value).
	 * @param {boolean} isCheckbox - If the field is a checkbox or radio.
	 * @param {boolean} isTomSelect - If the field is a TomSelect-enhanced field.
	 * @param {boolean} isFlatpickr - If the field is a Flatpickr-enhanced input.
	 * @param {boolean} keep - Whether to keep the current value.
	 * @param {boolean} save - Whether to restore a saved value.
	 */
	function restoreValue(child, saved, defVal, isCheckbox, isTomSelect, isFlatpickr, keep, save) {
		if (child.disabled || keep) return;

		if (isCheckbox) {
			if (save && saved !== null) {
				child.checked = saved;
			} else if (defVal) {
				child.checked = defVal;
			}
		} else if (isTomSelect) {
			if (save && saved !== null) {
				child.tomselect.setValue(child.multiple ? saved.toString().split(",") : saved);
			} else if (defVal && !child.value) {
				child.tomselect.setValue(child.multiple ? defVal.toString().split(",") : defVal);
			}
		} else if (isFlatpickr) {
			if (save && saved !== null) {
				child._flatpickr.setDate(saved);
			} else if (defVal && !child.value) {
				child._flatpickr.setDate(defVal);
			}
		} else {
			if (save && saved !== null) {
				child.value = saved;
			} else if (defVal && !child.value) {
				child.value = defVal;
			}
		}
		child.removeAttribute("data-hf-saved-value");
		child.dispatchEvent(new Event("input"));
	}

	/**
	 * Stores and clears the value of a child element when it is being hidden.
	 *
	 * @param {HTMLElement} child - The child element to store/clear.
	 * @param {boolean} isCheckbox - Whether the element is a checkbox/radio.
	 * @param {boolean} isTomSelect - Whether the element is a TomSelect instance.
	 * @param {boolean} isFlatpickr - Whether the element is a Flatpickr instance.
	 * @param {boolean} keep - Whether to preserve the current value.
	 * @param {boolean} save - Whether to store the current value before clearing.
	 */
	function saveAndClearValue(child, isCheckbox, isTomSelect, isFlatpickr, keep, save) {
		if (child.disabled || keep) return;

		if (isCheckbox) {
			if (save && child.checked) {
				child.dataset.hfSavedValue = child.checked;
			}
			child.checked = false;
		} else if (isTomSelect) {
			const val = child.tomselect.getValue();
			if (save && val) {
				child.dataset.hfSavedValue = child.multiple ? val.join(",") : child.value;
			}
			child.tomselect.clear();
		} else if (isFlatpickr) {
			if (save && child.value) {
				child.dataset.hfSavedValue = child.value;
			}
			child._flatpickr.clear();
		} else {
			if (save && child.value) {
				child.dataset.hfSavedValue = child.value;
			}
			child.value = null;
		}
		child.dispatchEvent(new Event("input"));
	}

	/**
	 * Restores the `required` attribute and label formatting for a child element when it becomes visible again.
	 *
	 * @param {HTMLElement} child - The child field element.
	 * @param {HTMLElement} container - The surrounding DOM container (usually .form-group parent).
	 * @param {boolean} isFlatpickr - Whether the field is a Flatpickr instance.
	 */
	function restoreRequired(child, container, isFlatpickr) {
		if (!child.hasAttribute("data-hf-required")) return;
		container.querySelectorAll("label.hf-required").forEach((label) => {
			label.classList.remove("hf-required");
			label.classList.add("required");
		});
		child.setAttribute("required", child.dataset.hfRequired);
		child.removeAttribute("data-hf-required");
		if (isFlatpickr) {
			const input = child._flatpickr._input;
			input.setAttribute("required", input.dataset.hfRequired);
			input.removeAttribute("data-hf-required");
		}
	}

	/**
	 * Stores and removes the `required` attribute of a child field before it is hidden.
	 * Also adjusts associated label classes for visual consistency.
	 *
	 * @param {HTMLElement} child - The child field element.
	 * @param {HTMLElement} container - The surrounding DOM container (usually .form-group parent).
	 * @param {boolean} isFlatpickr - Whether the field is a Flatpickr instance.
	 */
	function storeRequired(child, container, isFlatpickr) {
		if (!child.hasAttribute("required")) return;
		container.querySelectorAll("label.required").forEach((label) => {
			label.classList.remove("required");
			label.classList.add("hf-required");
		});
		child.dataset.hfRequired = child.getAttribute("required");
		child.removeAttribute("required");
		if (isFlatpickr) {
			const input = child._flatpickr._input;
			input.dataset.hfRequired = input.getAttribute("required");
			input.removeAttribute("required");
		}
	}

	/**
	 * Updates the visibility of a card container based on the number of visible children inside.
	 * Hides the card if all children are hidden.
	 *
	 * @param {HTMLElement} card - The card container element.
	 */
	function updateCardVisibility(child) {
		const card = child.closest(".card");
		if (!card) return;
		const groups = card.querySelectorAll(".form-group");
		const visible = Array.from(groups).some((g) => !g.classList.contains("d-none"));
		card.classList.toggle("d-none", !visible);
	}

	/**
	 * Parses a field value. Tries to parse JSON; if fails, returns the raw value or null if empty.
	 *
	 * @param {string|null} val - The raw value from an input or data attribute.
	 * @returns {*} - Parsed value (string, boolean, array, object, etc.).
	 */
	function parseFieldValue(val) {
		if (val === undefined || val === "") return null;
		try {
			return JSON.parse(val);
		} catch {
			return val;
		}
	}
})();
