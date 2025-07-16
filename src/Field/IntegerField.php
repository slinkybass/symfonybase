<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField as EasyField;

class IntegerField
{
    use FieldTrait;

    public const OPTION_PLUGIN_SLIDER = 'data-slider-field';

    public const OPTION_SLIDER_SHOW_INPUT = 'data-slider-show-input';
    public const OPTION_SLIDER_TOOLTIPS = 'data-slider-tooltips';
    public const OPTION_SLIDER_CONNECT = 'data-slider-connect';

    /** start connect types */
    public const SLIDER_CONNECT_UPPER = 'upper';
    public const SLIDER_CONNECT_LOWER = 'lower';
    /** end connect types */

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->setDefaultColumns(12);

        return $instance;
    }

    public function pluginSlider(bool $enable = true): self
    {
        $this->setHtmlAttribute(self::OPTION_PLUGIN_SLIDER, json_encode($enable));
        if ($enable) {
            $this->field->addAssetMapperEntries(Asset::new('form-type-slider')->onlyOnForms());
        } else {
            $this->field->getAsDto()->setAssets(new AssetsDto());
        }

        return $this;
    }

    public function sliderShowInput(bool $show = true): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_SHOW_INPUT, json_encode($show));

        return $this;
    }

    public function sliderTooltips(bool $tooltips = true): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_TOOLTIPS, json_encode($tooltips));

        return $this;
    }

    public function sliderConnect(string $type): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_CONNECT, $type);

        return $this;
    }

    public function setNumberFormat(string $numberFormat): self
    {
        $this->field->setNumberFormat($numberFormat);

        return $this;
    }

    public function setThousandsSeparator(string $separator): self
    {
        $this->field->setThousandsSeparator($separator);

        return $this;
    }
}
