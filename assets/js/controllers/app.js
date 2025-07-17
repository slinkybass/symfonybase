const App = (() => {
	const setMomentLocale = () => {
		moment.locale(document.querySelector("html")?.getAttribute("lang") ?? "en");
	};

	const createAutoCompleteFields = () => {
		const autocomplete = new Autocomplete();
		document.querySelectorAll('[data-ea-widget="ea-autocomplete"], [data-autocomplete-field="true"]').forEach((autocompleteElement) => {
			autocomplete.create(autocompleteElement);
		});
	};

	const persistSelectedTab = () => {
		// the ID of the selected tab is appended as a hash in the URL to persist it;
		// if the URL has a hash, try to look for a tab with that ID and show it
		const urlHash = window.location.hash;
		if (urlHash) {
			const selectedTabPaneId = urlHash.substring(1); // remove the leading '#' from the hash
			const selectedTabId = `tablist-${selectedTabPaneId}`;
			App.setTabAsActive(selectedTabId);
		}

		// update the page anchor when the selected tab changes
		document.querySelectorAll('a[data-bs-toggle="tab"]').forEach((tabElement) => {
			tabElement.addEventListener("shown.bs.tab", function (event) {
				const urlHash = "#" + event.target.getAttribute("href").substring(1);
				history.pushState({}, "", urlHash);
			});
		});
	};

	const createUnsavedFormChangesWarning = () => {
		[".ea-new-form", ".ea-edit-form"].forEach((formSelector) => {
			const form = document.querySelector(formSelector);
			if (null === form) {
				return;
			}

			// although DirtyForm supports passing a custom message to display,
			// modern browsers don't allow to display custom messages to protect users
			new DirtyForm(form);
		});
	};

	const createFieldsWithErrors = () => {
		document.querySelectorAll("form").forEach((form) => {
			var submitButtons = form.querySelectorAll('[type="submit"]');
			if (!submitButtons.length && form.id) {
				submitButtons = document.querySelectorAll('[type="submit"][form="' + form.id + '"]');
			}
			submitButtons.forEach((button) => {
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
							const secondElement = form.querySelector(`[name="${secondElementName}"]`);
							if (secondElement) {
								if (secondElement.value !== input.value) {
									const validityMessage = trans(translations.THE_VALUES_DO_NOT_MATCH, {}, "validators");
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
								const tab = document.querySelector(`[data-bs-toggle="tab"][href="#${tabPane.id}"]`);
								if (tab) {
									if (!firstTabError) {
										firstTabError = tab;
									}
									if (!tab.classList.contains("text-danger")) {
										tab.classList.add("text-danger");

										tab.addEventListener("click", function onTabClick() {
											tab.classList.remove("text-danger");
										});
									}
								}
							}

							input.addEventListener("input", handleInputEvent);
							input.addEventListener("change", handleInputEvent);

							function handleInputEvent() {
								input.classList.remove("is-invalid");
								const errorDiv = input.parentNode.querySelector(".invalid-feedback");
								if (errorDiv) {
									errorDiv.remove();
								}

								const name = input.name;
								const isRepeatedFirstField = name && name.endsWith("[first]");
								if (isRepeatedFirstField) {
									const secondElementName = name.replace("[first]", "[second]");
									const secondElement = form.querySelector(`[name="${secondElementName}"]`);
									if (secondElement) {
										secondElement.classList.remove("is-invalid");
										const errorDiv = secondElement.parentNode.querySelector(".invalid-feedback");
										if (errorDiv) {
											errorDiv.remove();
										}
									}
								}
								const isRepeatedSecondField = name && name.endsWith("[second]");
								if (isRepeatedSecondField) {
									const firstElementName = name.replace("[second]", "[first]");
									const firstElement = form.querySelector(`[name="${firstElementName}"]`);
									if (firstElement) {
										firstElement.classList.remove("is-invalid");
										const errorDiv = firstElement.parentNode.querySelector(".invalid-feedback");
										if (errorDiv) {
											errorDiv.remove();
										}
									}
								}

								input.removeEventListener("input", handleInputEvent);
								input.removeEventListener("change", handleInputEvent);

								if (input.type === "radio") {
									const name = input.name;
									form.querySelectorAll(`input[name="${name}"]`).forEach((radio) => {
										radio.classList.remove("is-invalid");
										const errorDiv = radio.parentNode.querySelector(".invalid-feedback");
										if (errorDiv) {
											errorDiv.remove();
										}
										radio.removeEventListener("input", handleInputEvent);
										radio.removeEventListener("change", handleInputEvent);
									});
								}
							}
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
	};

	const createLightboxes = () => {
		document.querySelectorAll('[data-action="zoom"]').forEach((link) => {
			link.addEventListener("click", (e) => {
				e.preventDefault();
				var img = '<img width="1400" height="900" src="' + link.getAttribute("href") + '">';
				basicLightbox.create(img).show();
			});
		});
	};

	const setTabAsActive = (tabItemId) => {
		const tabElement = document.getElementById(tabItemId);
		if (!tabElement) {
			return;
		}

		const Tab = bootstrap.Tab;
		const bootstrapTab = new Tab(tabElement);
		// when showing a tab, Bootstrap hides all the other tabs automatically
		bootstrapTab.show();
	};

	const preventMultipleFormSubmission = () => {
		document.querySelectorAll("form").forEach((form) => {
			form.addEventListener(
				"submit",
				() => {
					// this timeout is needed to include the disabled button into the submitted form
					setTimeout(() => {
						var submitButtons = form.querySelectorAll('[type="submit"]');
						if (!submitButtons.length && form.id) {
							submitButtons = document.querySelectorAll('[type="submit"][form="' + form.id + '"]');
						}
						submitButtons.forEach((button) => {
							button.classList.add("btn-loading");
						});
					}, 1);
				},
				false
			);
		});
	};

	return {
		setMomentLocale,
		createAutoCompleteFields,
		persistSelectedTab,
		createUnsavedFormChangesWarning,
		createFieldsWithErrors,
		createLightboxes,
		setTabAsActive,
		preventMultipleFormSubmission,
	};
})();

export default App;
