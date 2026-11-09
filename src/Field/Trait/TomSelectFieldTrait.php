<?php

namespace App\Field\Trait;

/**
 * TomSelect options for Choice / Enum / Association (`data-autocomplete-*`).
 * JS stays global in `app.js` (`helpers/autocomplete.js`); no form-type asset.
 *
 * EA attrs kept as-is: `data-ea-widget`, `data-ea-autocomplete-*`.
 * UI strings: `assets/js/fields/i18n/tomselect.*.js` (not Twig `data-ea-i18n-*`).
 * Not mapped: render/score/load callbacks, remote `firstUrl`, plugins beyond
 * clear/remove/dropdown_input/checkbox_options.
 */
trait TomSelectFieldTrait
{
    public const OPTION_PLUGIN = 'data-autocomplete-field';

    public const OPTION_AUTOCOMPLETE_MAX_ITEMS = 'data-autocomplete-max-items';
    public const OPTION_AUTOCOMPLETE_MAX_OPTIONS = 'data-autocomplete-max-options';
    public const OPTION_AUTOCOMPLETE_PERSIST = 'data-autocomplete-persist';
    public const OPTION_AUTOCOMPLETE_PRELOAD = 'data-autocomplete-preload';
    public const OPTION_AUTOCOMPLETE_OPEN_ON_FOCUS = 'data-autocomplete-open-on-focus';
    public const OPTION_AUTOCOMPLETE_CLOSE_AFTER_SELECT = 'data-autocomplete-close-after-select';
    public const OPTION_AUTOCOMPLETE_HIDE_SELECTED = 'data-autocomplete-hide-selected';
    public const OPTION_AUTOCOMPLETE_DUPLICATES = 'data-autocomplete-duplicates';
    public const OPTION_AUTOCOMPLETE_CREATE_ON_BLUR = 'data-autocomplete-create-on-blur';
    public const OPTION_AUTOCOMPLETE_ADD_PRECEDENCE = 'data-autocomplete-add-precedence';
    public const OPTION_AUTOCOMPLETE_SELECT_ON_TAB = 'data-autocomplete-select-on-tab';
    public const OPTION_AUTOCOMPLETE_DIACRITICS = 'data-autocomplete-diacritics';
    public const OPTION_AUTOCOMPLETE_DELIMITER = 'data-autocomplete-delimiter';
    public const OPTION_AUTOCOMPLETE_LOAD_THROTTLE = 'data-autocomplete-load-throttle';
    public const OPTION_AUTOCOMPLETE_SEARCH_CONJUNCTION = 'data-autocomplete-search-conjunction';
    public const OPTION_AUTOCOMPLETE_CLEAR_BUTTON = 'data-autocomplete-clear-button';
    public const OPTION_AUTOCOMPLETE_REMOVE_BUTTON = 'data-autocomplete-remove-button';
    public const OPTION_AUTOCOMPLETE_DROPDOWN_INPUT = 'data-autocomplete-dropdown-input';
    public const OPTION_AUTOCOMPLETE_CHECKBOX_OPTIONS = 'data-autocomplete-checkbox-options';

    /** EA / TomSelect create flag */
    public const OPTION_EA_ALLOW_ITEM_CREATE = 'data-ea-autocomplete-allow-item-create';
    public const OPTION_EA_RENDER_ITEMS_AS_HTML = 'data-ea-autocomplete-render-items-as-html';

    /** Same keys as EasyAdmin ChoiceField / AssociationField */
    public const OPTION_WIDGET = 'widget';
    public const WIDGET_AUTOCOMPLETE = 'autocomplete';
    public const WIDGET_NATIVE = 'native';
    public const OPTION_ESCAPE_HTML_CONTENTS = 'escapeHtml';

    /** preload */
    public const PRELOAD_FOCUS = 'focus';

    /** searchConjunction */
    public const SEARCH_AND = 'and';
    public const SEARCH_OR = 'or';

    /**
     * Toggle TomSelect (`data-ea-widget` via EA widget option + `data-autocomplete-field`).
     * Does not register a form-type asset — helper is loaded from `app.js`.
     */
    public function enablePlugin(bool $enable = true): self
    {
        $this->setCustomOption(self::OPTION_WIDGET, $enable ? self::WIDGET_AUTOCOMPLETE : self::WIDGET_NATIVE);
        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($enable));

        return $this;
    }

    /** Allow creating options not in the list (`create`). */
    public function setAllowItemCreate(bool $allow = true): self
    {
        $this->setHtmlAttribute(self::OPTION_EA_ALLOW_ITEM_CREATE, $allow ? 'true' : 'false');

        return $this;
    }

    /** Render option / item labels as HTML (EasyAdmin escapeHtml inverted). */
    public function renderAsHtml(bool $asHtml = true): self
    {
        $this->setCustomOption(self::OPTION_ESCAPE_HTML_CONTENTS, !$asHtml);
        $this->setHtmlAttribute(self::OPTION_EA_RENDER_ITEMS_AS_HTML, $asHtml ? 'true' : 'false');

        return $this;
    }

    /** Max selected items (`maxItems`; null = unlimited). */
    public function setMaxItems(?int $maxItems): self
    {
        $this->setHtmlAttribute(self::OPTION_AUTOCOMPLETE_MAX_ITEMS, null === $maxItems ? 'null' : (string) $maxItems);

        return $this;
    }

    /** Max options shown in the dropdown (`maxOptions`; null = unlimited). */
    public function setMaxOptions(?int $maxOptions): self
    {
        $this->setHtmlAttribute(self::OPTION_AUTOCOMPLETE_MAX_OPTIONS, null === $maxOptions ? 'null' : (string) $maxOptions);

        return $this;
    }

    /** Keep created items in the list after clear (`persist`). */
    public function isPersist(bool $persist = true): self
    {
        return $this->setDataAttr(self::OPTION_AUTOCOMPLETE_PERSIST, $persist);
    }

    /**
     * Preload remote options (`preload`: false, true, or `focus`).
     * Remote autocomplete defaults to `focus` in JS (EasyAdmin); pass false to require typing.
     *
     * @param bool|self::PRELOAD_FOCUS $preload
     */
    public function setPreload(bool|string $preload): self
    {
        if (\is_bool($preload)) {
            $this->setHtmlAttribute(self::OPTION_AUTOCOMPLETE_PRELOAD, json_encode($preload));
        } else {
            $this->setHtmlAttribute(self::OPTION_AUTOCOMPLETE_PRELOAD, $preload);
        }

        return $this;
    }

    /**
     * Open dropdown when the control is focused (`openOnFocus`, TomSelect default true).
     * With `dropdown_input` (default), false blocks opening (search lives in the dropdown).
     * Pair `false` with `showDropdownInput(false)` for remote fields without preload: type in
     * the control and open only when results arrive.
     */
    public function isOpenOnFocus(bool $open = true): self
    {
        return $this->setDataAttr(self::OPTION_AUTOCOMPLETE_OPEN_ON_FOCUS, $open);
    }

    /**
     * Close dropdown after selecting an option (`closeAfterSelect`).
     * Meaningful on multiple selects (TomSelect default false = stay open); single already closes.
     */
    public function isCloseAfterSelect(bool $close = true): self
    {
        return $this->setDataAttr(self::OPTION_AUTOCOMPLETE_CLOSE_AFTER_SELECT, $close);
    }

    /**
     * Hide already selected options from the dropdown (`hideSelected`).
     * TomSelect default: true for multiple, false for single — demos should flip the default.
     */
    public function isHideSelected(bool $hide = true): self
    {
        return $this->setDataAttr(self::OPTION_AUTOCOMPLETE_HIDE_SELECTED, $hide);
    }

    /** Allow the same option more than once (`duplicates`). */
    public function isDuplicates(bool $allow = true): self
    {
        return $this->setDataAttr(self::OPTION_AUTOCOMPLETE_DUPLICATES, $allow);
    }

    /** Create the item when the control blurs (`createOnBlur`). */
    public function isCreateOnBlur(bool $enable = true): self
    {
        return $this->setDataAttr(self::OPTION_AUTOCOMPLETE_CREATE_ON_BLUR, $enable);
    }

    /** Prefer adding a new item over selecting a match (`addPrecedence`). */
    public function isAddPrecedence(bool $enable = true): self
    {
        return $this->setDataAttr(self::OPTION_AUTOCOMPLETE_ADD_PRECEDENCE, $enable);
    }

    /** Select the highlighted option with Tab (`selectOnTab`). */
    public function isSelectOnTab(bool $enable = true): self
    {
        return $this->setDataAttr(self::OPTION_AUTOCOMPLETE_SELECT_ON_TAB, $enable);
    }

    /** Match diacritics in search (`diacritics`). */
    public function isDiacritics(bool $enable = true): self
    {
        return $this->setDataAttr(self::OPTION_AUTOCOMPLETE_DIACRITICS, $enable);
    }

    /** Delimiter for paste / create (`delimiter`). */
    public function setDelimiter(string $delimiter): self
    {
        $this->setHtmlAttribute(self::OPTION_AUTOCOMPLETE_DELIMITER, $delimiter);

        return $this;
    }

    /** Throttle remote `load` calls in ms (`loadThrottle`). */
    public function setLoadThrottle(int $ms): self
    {
        $this->setHtmlAttribute(self::OPTION_AUTOCOMPLETE_LOAD_THROTTLE, (string) $ms);

        return $this;
    }

    /**
     * How multiple search terms combine. Pass `SEARCH_AND` or `SEARCH_OR`.
     *
     * @param self::SEARCH_* $conjunction
     */
    public function setSearchConjunction(string $conjunction): self
    {
        $this->setHtmlAttribute(self::OPTION_AUTOCOMPLETE_SEARCH_CONJUNCTION, $conjunction);

        return $this;
    }

    /** Force clear_button plugin on/off (default: on when not required/disabled). */
    public function showClearButton(bool $show = true): self
    {
        return $this->setDataAttr(self::OPTION_AUTOCOMPLETE_CLEAR_BUTTON, $show);
    }

    /** Force remove_button plugin on/off (default: on when multiple). */
    public function showRemoveButton(bool $show = true): self
    {
        return $this->setDataAttr(self::OPTION_AUTOCOMPLETE_REMOVE_BUTTON, $show);
    }

    /** dropdown_input plugin (default: on). */
    public function showDropdownInput(bool $show = true): self
    {
        return $this->setDataAttr(self::OPTION_AUTOCOMPLETE_DROPDOWN_INPUT, $show);
    }

    /** checkbox_options plugin (useful for multiple). */
    public function showCheckboxOptions(bool $show = true): self
    {
        return $this->setDataAttr(self::OPTION_AUTOCOMPLETE_CHECKBOX_OPTIONS, $show);
    }
}
