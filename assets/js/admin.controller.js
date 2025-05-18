const Admin = (() => {
	const createFilters = () => {
		const filterButton = document.querySelector(".datagrid-filters .action-filters-button");
		if (null === filterButton) {
			return;
		}

		const filterModal = document.querySelector(filterButton.getAttribute("data-bs-target"));

		// this is needed to avoid errors when connection is slow
		filterButton.setAttribute("href", filterButton.getAttribute("data-href"));
		filterButton.removeAttribute("data-href");
		filterButton.classList.remove("disabled");

		filterButton.addEventListener("click", (event) => {
			const filterModalBody = filterModal.querySelector(".modal-body");
			filterModalBody.innerHTML = '<div class="w-100 text-center"><div class="spinner-border text-blue" role="status"></div></div>';

			fetch(filterButton.getAttribute("href"))
				.then((response) => {
					return response.text();
				})
				.then((text) => {
					filterModalBody.innerHTML = text;
					Admin.createFilterToggles();
				})
				.catch((error) => {
					console.error(error);
				});

			event.preventDefault();
		});

		const removeFilter = (filterField) => {
			filterField
				.closest("form")
				.querySelectorAll(`input[name^="filters[${filterField.dataset.filterProperty}]"]`)
				.forEach((filterFieldInput) => {
					filterFieldInput.remove();
				});

			filterField.remove();
		};

		document.querySelector("#modal-clear-button").addEventListener("click", () => {
			filterModal.querySelectorAll(".filter-field").forEach((filterField) => {
				removeFilter(filterField);
			});
			filterModal.querySelector("form").submit();
		});

		document.querySelector("#modal-apply-button").addEventListener("click", () => {
			filterModal.querySelectorAll(".filter-checkbox:not(:checked)").forEach((notAppliedFilter) => {
				removeFilter(notAppliedFilter.closest(".filter-field"));
			});
			filterModal.querySelector("form").submit();
		});
	};

	const createFilterToggles = () => {
		document.querySelectorAll(".filter-checkbox").forEach((filterCheckbox) => {
			filterCheckbox.addEventListener("change", () => {
				const filterToggleLink = filterCheckbox.nextElementSibling;
				const filterExpandedAttribute = filterCheckbox.nextElementSibling.getAttribute("aria-expanded");

				if ((filterCheckbox.checked && "false" === filterExpandedAttribute) || (!filterCheckbox.checked && "true" === filterExpandedAttribute)) {
					filterToggleLink.click();
				}
			});
		});

		document.querySelectorAll("form[data-ea-filters-form-id]").forEach((form) => {
			// TODO: when using the native datepicker, 'change' isn't fired unless you input the entire date + time information
			form.addEventListener("change", (event) => {
				if (event.target.classList.contains("filter-checkbox")) {
					return;
				}

				const filterCheckbox = event.target.closest(".form-check").querySelector(".filter-checkbox");
				if (!filterCheckbox.checked) {
					filterCheckbox.checked = true;
				}
			});
		});

		document.querySelectorAll("[data-ea-comparison-id]").forEach((comparisonWidget) => {
			comparisonWidget.addEventListener("change", (event) => {
				const comparisonWidget = event.currentTarget;
				const comparisonId = comparisonWidget.dataset.eaComparisonId;
				if (comparisonId === undefined) {
					return;
				}
				const secondValue = document.querySelector(`[data-ea-value2-of-comparison-id="${comparisonId}"]`);
				if (secondValue === null) {
					return;
				}
				Admin.toggleVisibilityClasses(secondValue, comparisonWidget.value !== "between");
			});
		});
	};

	const createBatchActions = () => {
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
					const lastIndex = parseInt(lastUpdatedRowCheckbox.dataset.rowIndex);
					const currentIndex = parseInt(e.target.dataset.rowIndex);
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

				if (rowCheckbox.checked) {
					row.classList.add("selected-row");
				} else {
					row.classList.remove("selected-row");
					selectAllCheckbox.checked = false;
				}

				const rowsAreSelected = 0 !== selectedRowCheckboxes.length;
				const contentTitle = document.querySelector(".content-header-title > .title");
				const filters = document.querySelector(".datagrid-filters");
				const globalActions = document.querySelector(".global-actions");
				const batchActions = document.querySelector(".batch-actions");

				if (null !== contentTitle) {
					Admin.toggleVisibilityClasses(contentTitle, rowsAreSelected);
				}
				if (null !== filters) {
					Admin.toggleVisibilityClasses(filters, rowsAreSelected);
				}
				if (null !== globalActions) {
					Admin.toggleVisibilityClasses(globalActions, rowsAreSelected);
				}
				if (null !== batchActions) {
					Admin.toggleVisibilityClasses(batchActions, !rowsAreSelected);
				}
			});
		});

		const modalTitle = document.querySelector("#batch-action-confirmation-title");
		const titleContentWithPlaceholders = modalTitle.textContent;

		document.querySelectorAll("[data-action-batch]").forEach((dataActionBatch) => {
			dataActionBatch.addEventListener("click", (event) => {
				event.preventDefault();

				const actionElement = event.currentTarget;
				const actionName = actionElement.textContent.trim() || actionElement.getAttribute("title");
				const selectedItems = document.querySelectorAll('input[type="checkbox"].form-batch-checkbox:checked');
				modalTitle.textContent = titleContentWithPlaceholders.replace("%action_name%", actionName).replace("%num_items%", selectedItems.length.toString());

				document.querySelector("#modal-batch-action-button").addEventListener("click", () => {
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
					for (let fieldName in batchFormFields) {
						const formField = document.createElement("input");
						formField.setAttribute("type", "hidden");
						formField.setAttribute("name", fieldName);
						formField.setAttribute("value", batchFormFields[fieldName]);
						batchForm.appendChild(formField);
					}

					document.body.appendChild(batchForm);
					batchForm.submit();
				});
			});
		});
	};

	const createModalWindowsForDeleteActions = () => {
		document.querySelectorAll(".action-delete").forEach((actionElement) => {
			actionElement.addEventListener("click", (event) => {
				event.preventDefault();
				document.querySelector("#modal-delete-button").addEventListener("click", () => {
					const deleteFormAction = actionElement.getAttribute("formaction");
					const deleteForm = document.querySelector("#delete-form");
					deleteForm.setAttribute("action", deleteFormAction);
					deleteForm.submit();
				});
			});
		});
	};

	const toggleVisibilityClasses = (element, removeVisibility) => {
		if (removeVisibility) {
			element.classList.remove("d-block");
			element.classList.add("d-none");
		} else {
			element.classList.remove("d-none");
			element.classList.add("d-block");
		}
	};

	return {
		createFilters,
		createFilterToggles,
		createBatchActions,
		createModalWindowsForDeleteActions,
		toggleVisibilityClasses,
	};
})();

export default Admin;
