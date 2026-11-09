/**
 * TomSelect autocomplete (EasyAdmin fork + `data-autocomplete-*` from TomSelectFieldTrait).
 *
 * Autor: slinkybass
 * Version: 1.0
 */

import TomSelect from "tom-select";
import { appLocale, boolAttr, intAttr, strAttr } from "../lib/plugin.js";
import tomselectEs from "../i18n/tomselect.es.js";

const ATTR = {
    maxItems: "data-autocomplete-max-items",
    maxOptions: "data-autocomplete-max-options",
    persist: "data-autocomplete-persist",
    preload: "data-autocomplete-preload",
    openOnFocus: "data-autocomplete-open-on-focus",
    closeAfterSelect: "data-autocomplete-close-after-select",
    hideSelected: "data-autocomplete-hide-selected",
    duplicates: "data-autocomplete-duplicates",
    createOnBlur: "data-autocomplete-create-on-blur",
    addPrecedence: "data-autocomplete-add-precedence",
    selectOnTab: "data-autocomplete-select-on-tab",
    diacritics: "data-autocomplete-diacritics",
    delimiter: "data-autocomplete-delimiter",
    loadThrottle: "data-autocomplete-load-throttle",
    searchConjunction: "data-autocomplete-search-conjunction",
    clearButton: "data-autocomplete-clear-button",
    removeButton: "data-autocomplete-remove-button",
    dropdownInput: "data-autocomplete-dropdown-input",
    checkboxOptions: "data-autocomplete-checkbox-options",
};

/** @type {Record<string, typeof tomselectEs>} */
const LOCALES = {
    es: tomselectEs,
};

const I18N_DEFAULTS = {
    optionCreate: "Add",
    noResults: "No results found",
    noMoreResults: "No more results",
    loadingMoreResults: "Loading more results…",
};

export default class Autocomplete {
    create(element) {
        // this avoids initializing the same field twice (TomSelect shows an error otherwise)
        if (element.classList.contains("tomselected")) {
            return;
        }

        // TomSelect only works with <select> and <input> elements
        if ("SELECT" !== element.tagName && "INPUT" !== element.tagName) {
            return;
        }

        const autocompleteEndpointUrl = element.getAttribute("data-ea-autocomplete-endpoint-url");
        if (null !== autocompleteEndpointUrl) {
            return this.#createAutocompleteWithRemoteData(element, autocompleteEndpointUrl);
        }

        const renderOptionsAsHtml = "true" === element.getAttribute("data-ea-autocomplete-render-items-as-html");
        if (renderOptionsAsHtml) {
            return this.#createAutocompleteWithHtmlContents(element);
        }

        return this.#createAutocomplete(element);
    }

    #i18n(key) {
        const pack = LOCALES[appLocale()] ?? {};
        return pack[key] ?? I18N_DEFAULTS[key] ?? key;
    }

    #getCommonConfig(element) {
        const optionCreateLabel = this.#i18n("optionCreate");
        const config = {
            render: {
                no_results: (data, escape) => `<div class="no-results">${this.#i18n("noResults")}</div>`,
                option_create: (data, escape) => `<div class="create">${optionCreateLabel} <strong>${escape(data.input)}</strong>&hellip;</div>`,
            },
            plugins: {
                dropdown_input: {},
            },
        };

        if (null === element.getAttribute("required") && null === element.getAttribute("disabled")) {
            config.plugins.clear_button = { title: "" };
        }

        if (null !== element.getAttribute("multiple")) {
            config.plugins.remove_button = { title: "" };
        }

        if (null !== element.getAttribute("data-ea-autocomplete-endpoint-url")) {
            config.plugins.virtual_scroll = {};
        }

        if ("true" === element.getAttribute("data-ea-autocomplete-allow-item-create")) {
            config.create = true;
        }

        return config;
    }

    /**
     * Apply TomSelectFieldTrait `data-autocomplete-*` overrides (after EA defaults).
     */
    #applyMappedOptions(element, config) {
        config.plugins ??= {};

        this.#assignNullableInt(element, ATTR.maxItems, config, "maxItems");
        this.#assignNullableInt(element, ATTR.maxOptions, config, "maxOptions");

        this.#assignBool(element, ATTR.persist, config, "persist");
        this.#assignBool(element, ATTR.openOnFocus, config, "openOnFocus");
        this.#assignBool(element, ATTR.closeAfterSelect, config, "closeAfterSelect");
        this.#assignBool(element, ATTR.hideSelected, config, "hideSelected");
        this.#assignBool(element, ATTR.duplicates, config, "duplicates");
        this.#assignBool(element, ATTR.createOnBlur, config, "createOnBlur");
        this.#assignBool(element, ATTR.addPrecedence, config, "addPrecedence");
        this.#assignBool(element, ATTR.selectOnTab, config, "selectOnTab");
        this.#assignBool(element, ATTR.diacritics, config, "diacritics");

        if (element.hasAttribute(ATTR.delimiter)) {
            config.delimiter = strAttr(element, ATTR.delimiter, ",");
        }

        if (element.hasAttribute(ATTR.loadThrottle)) {
            config.loadThrottle = intAttr(element, ATTR.loadThrottle, 300, 0);
        }

        if (element.hasAttribute(ATTR.searchConjunction)) {
            config.searchConjunction = strAttr(element, ATTR.searchConjunction, "and");
        }

        if (element.hasAttribute(ATTR.preload)) {
            const raw = strAttr(element, ATTR.preload, "false");
            if (raw === "focus") {
                config.preload = "focus";
            } else {
                config.preload = boolAttr(element, ATTR.preload, false);
            }
        }

        this.#assignPluginFlag(element, ATTR.clearButton, config, "clear_button", { title: "" });
        this.#assignPluginFlag(element, ATTR.removeButton, config, "remove_button", { title: "" });
        this.#assignPluginFlag(element, ATTR.dropdownInput, config, "dropdown_input", {});
        // form-check-input → Tabler/Bootstrap checkbox (accent uses app primary, not native blue)
        this.#assignPluginFlag(element, ATTR.checkboxOptions, config, "checkbox_options", {
            className: "form-check-input",
        });

        return config;
    }

    #assignBool(element, attr, config, key) {
        if (!element.hasAttribute(attr)) {
            return;
        }
        config[key] = boolAttr(element, attr, false);
    }

    #assignNullableInt(element, attr, config, key) {
        if (!element.hasAttribute(attr)) {
            return;
        }
        const raw = strAttr(element, attr, "");
        if (raw === "" || raw === "null") {
            config[key] = null;
            return;
        }
        const parsed = Number.parseInt(raw, 10);
        if (Number.isFinite(parsed)) {
            config[key] = parsed;
        }
    }

    #assignPluginFlag(element, attr, config, pluginName, pluginOptions) {
        if (!element.hasAttribute(attr)) {
            return;
        }
        if (boolAttr(element, attr, false)) {
            config.plugins[pluginName] = pluginOptions;
        } else {
            delete config.plugins[pluginName];
        }
    }

    #createAutocomplete(element) {
        let config = this.#mergeObjects(this.#getCommonConfig(element), {
            maxOptions: null,
        });

        if (this.#hasPreferredChoices(element)) {
            const { options, optgroups } = this.#extractOptionsWithOptgroups(element, false);
            config = this.#mergeObjects(config, {
                options: options,
                optgroups: optgroups,
                optgroupField: "optgroup",
                lockOptgroupOrder: true,
                valueField: "value",
                labelField: "text",
                searchField: ["text"],
            });
        }

        return this.#initializeTomSelect(element, config);
    }

    #createAutocompleteWithHtmlContents(element) {
        let config = this.#mergeObjects(this.#getCommonConfig(element), {
            valueField: "value",
            labelField: "label_raw",
            searchField: ["label_text"],
            maxOptions: null,
            render: {
                item: (item, escape) => `<div>${item.label_raw}</div>`,
                option: (item, escape) => `<div>${item.label_raw}</div>`,
            },
        });

        if (this.#hasPreferredChoices(element)) {
            const { options, optgroups } = this.#extractOptionsWithOptgroups(element, true);
            config = this.#mergeObjects(config, {
                options: options,
                optgroups: optgroups,
                optgroupField: "optgroup",
                lockOptgroupOrder: true,
            });
        } else {
            config.options = this.#extractOptions(element, true);
        }

        return this.#initializeTomSelect(element, config);
    }

    #createAutocompleteWithRemoteData(element, autocompleteEndpointUrl) {
        const renderOptionsAsHtml = "true" === element.getAttribute("data-ea-autocomplete-render-items-as-html");
        const autocomplete = this;
        let config = this.#mergeObjects(this.#getCommonConfig(element), {
            valueField: "entityId",
            labelField: "entityAsString",
            searchField: ["entityAsString"],
            // each result may define an 'entityGroup' (the group_by option of the form type)
            // to render it under an <optgroup>-like header in the dropdown
            optgroupField: "entityGroup",
            lockOptgroupOrder: true,
            firstUrl: (query) => {
                return `${autocompleteEndpointUrl}&query=${encodeURIComponent(query)}`;
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
                        callback(json.results, autocomplete.#extractOptgroupsFromResults(json.results));
                    })
                    .catch(() => callback());
            },
            preload: "focus",
            maxOptions: null,
            // on remote calls, we don't want tomselect to further filter the results by "entityAsString"
            // this override causes all results to be returned with the sorting from the server
            score: (search) => (item) => 1,
            render: {
                option: (item, escape) => `<div>${renderOptionsAsHtml ? item.entityAsString : escape(item.entityAsString)}</div>`,
                item: (item, escape) => `<div>${renderOptionsAsHtml ? item.entityAsString : escape(item.entityAsString)}</div>`,
                loading_more: (data, escape) => `<div class="loading-more-results">${this.#i18n("loadingMoreResults")}</div>`,
                no_more_results: (data, escape) => `<div class="no-more-results">${this.#i18n("noMoreResults")}</div>`,
                no_results: (data, escape) => `<div class="no-results">${this.#i18n("noResults")}</div>`,
                option_create: (data, escape) => `<div class="create">${this.#i18n("optionCreate")} <strong>${escape(data.input)}</strong>&hellip;</div>`,
            },
        });

        // when using group_by, the server renders the selected items inside <optgroup> elements;
        // extract them keyed by the group LABEL so they merge with the groups of the Ajax results
        // (TomSelect's own DOM import assigns numeric ids to these groups, which would duplicate the headers)
        if (null !== element.querySelector("optgroup")) {
            const { options, optgroups } = this.#extractRemoteOptionsWithOptgroups(element);
            config = this.#mergeObjects(config, { options: options, optgroups: optgroups });
        }

        return this.#initializeTomSelect(element, config);
    }

    #initializeTomSelect(element, config) {
        config = this.#applyMappedOptions(element, config);

        element.dispatchEvent(new CustomEvent("ea.autocomplete.pre-connect", { detail: { config, prefix: "autocomplete" }, bubbles: true }));

        const tomSelect = new TomSelect(element, config);

        element.dispatchEvent(new CustomEvent("ea.autocomplete.connect", { detail: { tomSelect, config, prefix: "autocomplete" }, bubbles: true }));

        return tomSelect;
    }

    #stripTags(string) {
        return string.replace(/(<([^>]+)>)/gi, "");
    }

    #mergeObjects(object1, object2) {
        return { ...object1, ...object2 };
    }

    #hasPreferredChoices(element) {
        for (let i = 0; i < element.options?.length; i++) {
            if (this.#isPreferredChoicesSeparator(element.options[i])) {
                return true;
            }
        }
        return false;
    }

    #isPreferredChoicesSeparator(option) {
        // Symfony renders preferred_choices with a disabled separator option containing only dashes
        return option.disabled && option.text.trim().match(/^-+$/);
    }

    #extractOptions(element, withHtmlSupport) {
        const options = [];
        for (let i = 0; i < element.options.length; i++) {
            const opt = element.options[i];
            if (opt.value === "" || this.#isPreferredChoicesSeparator(opt)) {
                continue;
            }

            if (withHtmlSupport) {
                options.push({
                    value: opt.value,
                    label_text: this.#stripTags(opt.text),
                    label_raw: opt.text,
                });
            } else {
                options.push({
                    value: opt.value,
                    text: opt.text,
                });
            }
        }
        return options;
    }

    #extractOptionsWithOptgroups(element, withHtmlSupport) {
        const options = [];
        const optgroups = [
            { value: "preferred", label: "", $order: 1 },
            { value: "regular", label: "", $order: 2 },
        ];

        let foundSeparator = false;
        const seenValues = new Set();

        for (let i = 0; i < element.options.length; i++) {
            const opt = element.options[i];

            if (this.#isPreferredChoicesSeparator(opt)) {
                foundSeparator = true;
                continue;
            }

            // skip empty placeholder options
            if (opt.value === "") {
                continue;
            }

            // avoid duplicates (preferred choices appear twice in the HTML)
            if (foundSeparator && seenValues.has(opt.value)) {
                continue;
            }

            const optionData = withHtmlSupport
                ? {
                      value: opt.value,
                      label_text: this.#stripTags(opt.text),
                      label_raw: opt.text,
                      optgroup: foundSeparator ? "regular" : "preferred",
                  }
                : {
                      value: opt.value,
                      text: opt.text,
                      optgroup: foundSeparator ? "regular" : "preferred",
                  };

            options.push(optionData);

            if (!foundSeparator) {
                seenValues.add(opt.value);
            }
        }

        return { options, optgroups };
    }

    #extractOptgroupsFromResults(results) {
        const optgroups = [];
        const seenGroups = new Set();

        (results || []).forEach((result) => {
            const groups = Array.isArray(result.entityGroup) ? result.entityGroup : [result.entityGroup];
            groups.forEach((group) => {
                if (null === group || undefined === group || seenGroups.has(group)) {
                    return;
                }

                seenGroups.add(group);
                // 'value' is the field TomSelect uses as the optgroup id (optgroupValueField)
                optgroups.push({ value: group, label: group });
            });
        });

        return optgroups;
    }

    #extractRemoteOptionsWithOptgroups(element) {
        const options = [];
        const optgroups = [];
        const seenGroups = new Set();

        const addOption = (opt, groupLabel) => {
            // skip empty placeholder options
            if ("" === opt.value) {
                return;
            }

            // use textContent (like TomSelect's own DOM import): with renderAsHtml, the HTML
            // markup is stored escaped as the option text and rendered raw later by render.item
            const optionData = { ...opt.dataset, entityId: opt.value, entityAsString: opt.textContent };
            if (undefined !== groupLabel) {
                optionData.entityGroup = groupLabel;
            }
            options.push(optionData);
        };

        for (let i = 0; i < element.children.length; i++) {
            const child = element.children[i];
            if ("OPTGROUP" === child.tagName) {
                if (!seenGroups.has(child.label)) {
                    seenGroups.add(child.label);
                    optgroups.push({ value: child.label, label: child.label });
                }
                for (let j = 0; j < child.children.length; j++) {
                    addOption(child.children[j], child.label);
                }
            } else if ("OPTION" === child.tagName) {
                addOption(child);
            }
        }

        return { options, optgroups };
    }
}
