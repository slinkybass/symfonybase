<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField as EasyField;

class FloatField
{
    use FieldTrait;

    public const OPTION_HTML5 = 'html5';
    public const OPTION_MAX = 'max';
    public const OPTION_MIN = 'min';
    public const OPTION_STEP = 'step';

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
            ->setDecimals(2)
            ->setStep(0.1)
            ->setFormTypeOption(self::OPTION_HTML5, true)
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

    public function showInputSlider(bool $show = true): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_SHOW_INPUT, json_encode($show));

        return $this;
    }

    public function showTooltipSlider(bool $tooltips = true): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_TOOLTIPS, json_encode($tooltips));

        return $this;
    }

    public function connectSlider(string $type): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_CONNECT, $type);

        return $this;
    }

    public function setMax(int|float|null $val): self
    {
        $this->setHtmlAttribute(self::OPTION_MAX, $val);

        return $this;
    }

    public function setMin(int|float|null $val): self
    {
        $this->setHtmlAttribute(self::OPTION_MIN, $val);

        return $this;
    }

    public function setStep(int|float|null $step): self
    {
        $this->field->setHtmlAttribute(self::OPTION_STEP, $step);

        return $this;
    }

    public function setDecimals(int $decimals): self
    {
        $this->field->setNumDecimals($decimals);

        return $this;
    }

    public function setRoundingMode(int $mode): self
    {
        $this->field->setRoundingMode($mode);

        return $this;
    }

    public function storedAsString(bool $asString = true): self
    {
        $this->field->setStoredAsString($asString);

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

    public function setDecimalSeparator(string $separator): self
    {
        $this->field->setDecimalSeparator($separator);

        return $this;
    }
}
