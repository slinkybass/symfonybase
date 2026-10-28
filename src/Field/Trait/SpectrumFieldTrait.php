<?php

namespace App\Field\Trait;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;

/**
 * Spectrum options for `ColorField` (`data-color-*`).
 *
 * Not mapped: hooks (beforeShow/move/change/show/hide), appendTo, offset,
 * disabled (use FieldTrait), locale as object.
 */
trait SpectrumFieldTrait
{
    public const OPTION_PLUGIN = 'data-color-field';

    public const OPTION_COLOR_TYPE = 'data-color-type';
    public const OPTION_COLOR_COLOR = 'data-color-color';
    public const OPTION_COLOR_PREFERRED_FORMAT = 'data-color-preferred-format';
    public const OPTION_COLOR_SHOW_INPUT = 'data-color-show-input';
    public const OPTION_COLOR_SHOW_INITIAL = 'data-color-show-initial';
    public const OPTION_COLOR_ALLOW_EMPTY = 'data-color-allow-empty';
    public const OPTION_COLOR_SHOW_ALPHA = 'data-color-show-alpha';
    public const OPTION_COLOR_SHOW_BUTTONS = 'data-color-show-buttons';
    public const OPTION_COLOR_CLICKOUT_FIRES_CHANGE = 'data-color-clickout-fires-change';
    public const OPTION_COLOR_SHOW_PALETTE = 'data-color-show-palette';
    public const OPTION_COLOR_PALETTE_ONLY = 'data-color-palette-only';
    public const OPTION_COLOR_HIDE_AFTER_PALETTE_SELECT = 'data-color-hide-after-palette-select';
    public const OPTION_COLOR_TOGGLE_PALETTE_ONLY = 'data-color-toggle-palette-only';
    public const OPTION_COLOR_SHOW_SELECTION_PALETTE = 'data-color-show-selection-palette';
    public const OPTION_COLOR_LOCAL_STORAGE_KEY = 'data-color-local-storage-key';
    public const OPTION_COLOR_MAX_SELECTION_SIZE = 'data-color-max-selection-size';
    public const OPTION_COLOR_PALETTE = 'data-color-palette';
    public const OPTION_COLOR_SELECTION_PALETTE = 'data-color-selection-palette';
    public const OPTION_COLOR_CONTAINER_CLASS_NAME = 'data-color-container-class-name';
    public const OPTION_COLOR_REPLACER_CLASS_NAME = 'data-color-replacer-class-name';
    public const OPTION_COLOR_THEME = 'data-color-theme';
    public const OPTION_COLOR_LOCALE = 'data-color-locale';
    public const OPTION_COLOR_CANCEL_TEXT = 'data-color-cancel-text';
    public const OPTION_COLOR_CHOOSE_TEXT = 'data-color-choose-text';
    public const OPTION_COLOR_TOGGLE_PALETTE_MORE_TEXT = 'data-color-toggle-palette-more-text';
    public const OPTION_COLOR_TOGGLE_PALETTE_LESS_TEXT = 'data-color-toggle-palette-less-text';
    public const OPTION_COLOR_CLEAR_TEXT = 'data-color-clear-text';
    public const OPTION_COLOR_NO_COLOR_SELECTED_TEXT = 'data-color-no-color-selected-text';

    /** types */
    public const COLOR_TYPE_TEXT = 'text';
    public const COLOR_TYPE_COMPONENT = 'component';
    public const COLOR_TYPE_COLOR = 'color';
    public const COLOR_TYPE_FLAT = 'flat';

    /** preferredFormat */
    public const COLOR_FORMAT_HEX = 'hex';
    public const COLOR_FORMAT_HEX3 = 'hex3';
    public const COLOR_FORMAT_HSL = 'hsl';
    public const COLOR_FORMAT_RGB = 'rgb';
    public const COLOR_FORMAT_NAME = 'name';

    /** theme */
    public const COLOR_THEME_LIGHT = 'sp-light';
    public const COLOR_THEME_DARK = 'sp-dark';

    protected function enableMappedPlugin(string $entry, string $flagAttr, bool $enable): self
    {
        $this->dto->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new($entry)->onlyOnForms());
        }
        $this->setHtmlAttribute($flagAttr, json_encode($enable));

        return $this;
    }

    protected function setColorBoolAttr(string $option, bool $value): self
    {
        $this->setHtmlAttribute($option, json_encode($value));

        return $this;
    }

    protected function setColorJsonAttr(string $option, mixed $value): self
    {
        $this->setHtmlAttribute($option, json_encode($value));

        return $this;
    }

    /**
     * Spectrum UI mode.
     *
     * @param self::COLOR_TYPE_* $type
     */
    public function setType(string $type): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_TYPE, $type);

        return $this;
    }

    /** Initial color override (otherwise the input value is used). */
    public function setColor(?string $color): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_COLOR, $color);

        return $this;
    }

    /**
     * Preferred output / display format.
     *
     * @param self::COLOR_FORMAT_*|string $preferredFormat
     */
    public function setPreferredFormat(string $preferredFormat): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_PREFERRED_FORMAT, $preferredFormat);

        return $this;
    }

    /** Free-form color text input inside the picker. */
    public function showInput(bool $show = true): self
    {
        return $this->setColorBoolAttr(self::OPTION_COLOR_SHOW_INPUT, $show);
    }

    /** Show the color that was set when the picker opened. */
    public function showInitial(bool $show = true): self
    {
        return $this->setColorBoolAttr(self::OPTION_COLOR_SHOW_INITIAL, $show);
    }

    /** Allow clearing the color (empty value). */
    public function isAllowEmpty(bool $allow = true): self
    {
        return $this->setColorBoolAttr(self::OPTION_COLOR_ALLOW_EMPTY, $allow);
    }

    /** Allow alpha / transparency. */
    public function showAlpha(bool $show = true): self
    {
        return $this->setColorBoolAttr(self::OPTION_COLOR_SHOW_ALPHA, $show);
    }

    /** Show Choose / Cancel buttons. */
    public function showButtons(bool $show = true): self
    {
        return $this->setColorBoolAttr(self::OPTION_COLOR_SHOW_BUTTONS, $show);
    }

    /** Commit the color when clicking outside the picker. */
    public function isClickoutFiresChange(bool $enable = true): self
    {
        return $this->setColorBoolAttr(self::OPTION_COLOR_CLICKOUT_FIRES_CHANGE, $enable);
    }

    /** Show the color palette panel. */
    public function showPalette(bool $show = true): self
    {
        return $this->setColorBoolAttr(self::OPTION_COLOR_SHOW_PALETTE, $show);
    }

    /** Restrict the picker to the palette only. */
    public function isPaletteOnly(bool $paletteOnly = true): self
    {
        return $this->setColorBoolAttr(self::OPTION_COLOR_PALETTE_ONLY, $paletteOnly);
    }

    /** Close the picker after a palette swatch is chosen. */
    public function hideAfterPaletteSelect(bool $hide = true): self
    {
        return $this->setColorBoolAttr(self::OPTION_COLOR_HIDE_AFTER_PALETTE_SELECT, $hide);
    }

    /** Show a toggle to expand beyond palette-only mode. */
    public function isTogglePaletteOnly(bool $enable = true): self
    {
        return $this->setColorBoolAttr(self::OPTION_COLOR_TOGGLE_PALETTE_ONLY, $enable);
    }

    /** Keep a selection palette of recently chosen colors. */
    public function showSelectionPalette(bool $show = true): self
    {
        return $this->setColorBoolAttr(self::OPTION_COLOR_SHOW_SELECTION_PALETTE, $show);
    }

    /** Browser localStorage key shared across Spectrum instances. */
    public function setLocalStorageKey(?string $key): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_LOCAL_STORAGE_KEY, $key);

        return $this;
    }

    /** Max colors kept in the selection palette. */
    public function setMaxSelectionSize(int $size): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_MAX_SELECTION_SIZE, $size);

        return $this;
    }

    /**
     * Fixed palette rows (`string[][]`).
     *
     * @param list<list<string>>|null $palette
     */
    public function setPalette(?array $palette): self
    {
        return $this->setColorJsonAttr(self::OPTION_COLOR_PALETTE, $palette ?? []);
    }

    /**
     * Initial selection-palette colors (`string[]`).
     *
     * @param list<string>|null $selectionPalette
     */
    public function setSelectionPalette(?array $selectionPalette): self
    {
        return $this->setColorJsonAttr(self::OPTION_COLOR_SELECTION_PALETTE, $selectionPalette ?? []);
    }

    /** Extra CSS class on the Spectrum container. */
    public function setContainerClassName(?string $className): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_CONTAINER_CLASS_NAME, $className);

        return $this;
    }

    /** Extra CSS class on the Spectrum replacer. */
    public function setReplacerClassName(?string $className): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_REPLACER_CLASS_NAME, $className);

        return $this;
    }

    /**
     * Spectrum theme class (`sp-light` / `sp-dark`).
     *
     * @param self::COLOR_THEME_*|string $theme
     */
    public function setTheme(string $theme): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_THEME, $theme);

        return $this;
    }

    /** Spectrum l10n key for this instance. */
    public function setPickerLocale(string $locale): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_LOCALE, $locale);

        return $this;
    }

    /** Label for the Cancel button. */
    public function setCancelText(string $text): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_CANCEL_TEXT, $text);

        return $this;
    }

    /** Label for the Choose button. */
    public function setChooseText(string $text): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_CHOOSE_TEXT, $text);

        return $this;
    }

    /** Label for the palette “more” toggle. */
    public function setTogglePaletteMoreText(string $text): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_TOGGLE_PALETTE_MORE_TEXT, $text);

        return $this;
    }

    /** Label for the palette “less” toggle. */
    public function setTogglePaletteLessText(string $text): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_TOGGLE_PALETTE_LESS_TEXT, $text);

        return $this;
    }

    /** Label / title for the clear-color control. */
    public function setClearText(string $text): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_CLEAR_TEXT, $text);

        return $this;
    }

    /** Text shown when no color is selected. */
    public function setNoColorSelectedText(string $text): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_NO_COLOR_SELECTED_TEXT, $text);

        return $this;
    }
}
