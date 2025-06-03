import TomSelect from "tom-select";

export default class Autocomplete {
	create(element) {
		// this avoids initializing the same field twice (TomSelect shows an error otherwise)
		if (element.classList.contains("tomselected")) {
			return;
		}

		const autocompleteEndpointUrl = element.getAttribute("data-ea-autocomplete-endpoint-url") ?? element.getAttribute("data-autocomplete-endpoint-url");
		if (null !== autocompleteEndpointUrl) {
			return this.#createAutocompleteWithRemoteData(element, autocompleteEndpointUrl);
		}

		const renderOptionsAsHtmlVal = element.getAttribute("data-ea-autocomplete-render-items-as-html") ?? element.getAttribute("data-autocomplete-render-items-as-html");
		const renderOptionsAsHtml = "true" === renderOptionsAsHtmlVal;
		if (renderOptionsAsHtml) {
			return this.#createAutocompleteWithHtmlContents(element);
		}

		return this.#createAutocomplete(element);
	}

	#getCommonConfig(element) {
		const config = {
			render: {
				no_results: function (data, escape) {
					const noResultsFound = element.getAttribute("data-ea-i18n-no-results-found") || element.getAttribute("data-autocomplete-no-results-found");
					return `<div class="no-results">${noResultsFound}</div>`;
				},
			},
			plugins: {
				change_listener: {},
			},
		};

		if (null === element.getAttribute("required") && null === element.getAttribute("disabled")) {
			config.plugins.clear_button = { title: "" };
		}

		if (null !== element.getAttribute("multiple")) {
			config.plugins.remove_button = { title: "" };
			config.plugins.no_active_items = {};
		}

		const autocompleteEndpointUrl = element.getAttribute("data-ea-autocomplete-endpoint-url") ?? element.getAttribute("data-autocomplete-endpoint-url");
		if (null !== autocompleteEndpointUrl) {
			config.plugins.virtual_scroll = {};
		}

		const allowItemCreate = element.getAttribute("data-ea-autocomplete-allow-item-create") ?? element.getAttribute("data-autocomplete-allow-item-create");
		if ("true" === allowItemCreate) {
			config.create = true;
		}

		return config;
	}

	#createAutocomplete(element) {
		const config = this.#mergeObjects(this.#getCommonConfig(element), {
			maxOptions: null,
		});

		return new TomSelect(element, config);
	}

	#createAutocompleteWithHtmlContents(element) {
		const autoSelectOptions = [];
		for (let i = 0; i < element.options.length; i++) {
			const label = element.options[i].text;
			const value = element.options[i].value;

			autoSelectOptions.push({
				label_text: this.#stripTags(label),
				label_raw: label,
				value: value,
			});
		}

		const config = this.#mergeObjects(this.#getCommonConfig(element), {
			valueField: "value",
			labelField: "label_raw",
			searchField: ["label_text"],
			options: autoSelectOptions,
			maxOptions: null,
			render: {
				item: function (item, escape) {
					return `<div>${item.label_raw}</div>`;
				},
				option: function (item, escape) {
					return `<div>${item.label_raw}</div>`;
				},
			},
		});

		return new TomSelect(element, config);
	}

	#createAutocompleteWithRemoteData(element, autocompleteEndpointUrl) {
		const renderOptionsAsHtmlVal = element.getAttribute("data-ea-autocomplete-render-items-as-html") ?? element.getAttribute("data-autocomplete-render-items-as-html");
		const renderOptionsAsHtml = "true" === renderOptionsAsHtmlVal;
		const config = this.#mergeObjects(this.#getCommonConfig(element), {
			valueField: "entityId",
			labelField: "entityAsString",
			searchField: ["entityAsString"],
			firstUrl: (query) => {
				return autocompleteEndpointUrl + "&query=" + encodeURIComponent(query);
			},
			// VERY IMPORTANT: use 'function (query, callback) { ... }' instead of the
			// '(query, callback) => { ... }' syntax because, otherwise,
			// the 'this.XXX' calls inside of this method fail
			load: function (query, callback) {
				const url = this.getUrl(query);
				fetch(url)
					.then((response) => response.json())
					// important: next_url must be set before invoking callback()
					.then((json) => {
						this.setNextUrl(query, json.next_page);
						callback(json.results);
					})
					.catch(() => callback());
			},
			preload: "focus",
			maxOptions: null,
			// on remote calls, we don't want tomselect to further filter the results by "entityAsString"
			// this override causes all results to be returned with the sorting from the server
			score: function (search) {
				return function (item) {
					return 1;
				};
			},
			render: {
				option: function (item, escape) {
					return `<div>${renderOptionsAsHtml ? item.entityAsString : escape(item.entityAsString)}</div>`;
				},
				item: function (item, escape) {
					return `<div>${renderOptionsAsHtml ? item.entityAsString : escape(item.entityAsString)}</div>`;
				},
				loading_more: function (data, escape) {
					const loadingMoreResults = element.getAttribute("data-ea-i18n-loading-more-results") || element.getAttribute("data-autocomplete-loading-more-results");
					return `<div class="loading-more-results">${loadingMoreResults}</div>`;
				},
				no_more_results: function (data, escape) {
					const noMoreResults = element.getAttribute("data-ea-i18n-no-more-results") || element.getAttribute("data-autocomplete-no-more-results");
					return `<div class="no-more-results">${noMoreResults}</div>`;
				},
				no_results: function (data, escape) {
					const noResultsFound = element.getAttribute("data-ea-i18n-no-results-found") || element.getAttribute("data-autocomplete-no-results-found");
					return `<div class="no-results">${noResultsFound}</div>`;
				},
			},
		});

		return new TomSelect(element, config);
	}

	#stripTags(string) {
		return string.replace(/(<([^>]+)>)/gi, "");
	}

	#mergeObjects(object1, object2) {
		return { ...object1, ...object2 };
	}
}
