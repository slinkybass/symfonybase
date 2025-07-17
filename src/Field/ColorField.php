<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField as EasyField;

class ColorField
{
    use FieldTrait;

    public const OPTION_PLUGIN = 'data-color-field';

    public const OPTION_COLOR_TYPE = 'data-color-type';
    public const OPTION_COLOR_PREFERRED_FORMAT = 'data-color-preferred-format';
    public const OPTION_COLOR_SHOW_PALETTE = 'data-color-show-palette';
    public const OPTION_COLOR_PALETTE_ONLY = 'data-color-palette-only';
    public const OPTION_COLOR_SHOW_ALPHA = 'data-color-show-alpha';
    public const OPTION_COLOR_HIDE_AFTER_PALETTE_SELECT = 'hide-after-palette-select';

    /** start types */
    public const COLOR_TYPE_TEXT = 'text';
    public const COLOR_TYPE_COMPONENT = 'component';
    public const COLOR_TYPE_COLOR = 'color';
    public const COLOR_TYPE_FLAT = 'flat';
    /** end types */

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-color')->onlyOnForms())
            ->plugin()
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $enable = true): self
    {
        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($enable));

        return $this;
    }

    public function setType(string $type): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_TYPE, $type);

        return $this;
    }

    public function setPreferredFormat(string $preferredFormat): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_PREFERRED_FORMAT, $preferredFormat);

        return $this;
    }

    public function showPalette(bool $show = true): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_SHOW_PALETTE, json_encode($show));

        return $this;
    }

    public function isPaletteOnly(bool $paletteOnly = true): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_PALETTE_ONLY, json_encode($paletteOnly));

        return $this;
    }

    public function showAlpha(bool $show = true): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_SHOW_ALPHA, json_encode($show));

        return $this;
    }

    public function hideAfterPaletteSelect(bool $hide = true): self
    {
        $this->setHtmlAttribute(self::OPTION_COLOR_HIDE_AFTER_PALETTE_SELECT, json_encode($hide));

        return $this;
    }

    public function showSample(bool $show = true): self
    {
        $this->field->showSample($show);

        return $this;
    }

    public function showValue(bool $show = true): self
    {
        $this->field->showValue($show);

        return $this;
    }
}
