<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField as EasyField;

class FloatField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const OPTION_HTML5 = 'html5';
    public const OPTION_MAX = 'max';
    public const OPTION_MIN = 'min';
    public const OPTION_STEP = 'step';

    public const OPTION_PLUGIN_SLIDER = 'data-slider-field';

    public const OPTION_SLIDER_SHOW_INPUT = 'data-slider-show-input';
    public const OPTION_SLIDER_TOOLTIPS = 'data-slider-tooltips';
    public const OPTION_SLIDER_CONNECT = 'data-slider-connect';
    public const OPTION_SLIDER_PIPS = 'data-slider-pips';

    /** connect types */
    public const SLIDER_CONNECT_UPPER = 'upper';
    public const SLIDER_CONNECT_LOWER = 'lower';

    public static function new(string $propertyName, $label = null): self
    {
        $field = new self();
        $field->innerField = EasyField::new($propertyName, $label);
        $field->initField($field->innerField);
        $field
            ->setDecimals(2)
            ->setStep(0.1)
            ->setFormTypeOption(self::OPTION_HTML5, true);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
    }

    public function pluginSlider(bool $enable = true): self
    {
        $this->setHtmlAttribute(self::OPTION_PLUGIN_SLIDER, json_encode($enable));
        if ($enable) {
            $this->innerField->addAssetMapperEntries(Asset::new('form-type-slider')->onlyOnForms());
        } else {
            $this->innerField->getAsDto()->setAssets(new AssetsDto());
        }

        return $this;
    }

    public function sliderShowInput(bool $show = true): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_SHOW_INPUT, json_encode($show));

        return $this;
    }

    public function sliderShowTooltip(bool $tooltip = true): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_TOOLTIPS, json_encode($tooltip));

        return $this;
    }

    public function sliderConnectUpper(): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_CONNECT, self::SLIDER_CONNECT_UPPER);

        return $this;
    }

    public function sliderConnectLower(): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_CONNECT, self::SLIDER_CONNECT_LOWER);

        return $this;
    }

    public function sliderPips(bool $pips = true): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_PIPS, json_encode($pips));

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
        $this->innerField->setHtmlAttribute(self::OPTION_STEP, $step);

        return $this;
    }

    public function setDecimals(int $decimals): self
    {
        $this->innerField->setNumDecimals($decimals);

        return $this;
    }

    public function setRoundingMode(int $mode): self
    {
        $this->innerField->setRoundingMode($mode);

        return $this;
    }

    public function storedAsString(bool $asString = true): self
    {
        $this->innerField->setStoredAsString($asString);

        return $this;
    }

    public function setNumberFormat(string $numberFormat): self
    {
        $this->innerField->setNumberFormat($numberFormat);

        return $this;
    }

    public function setThousandsSeparator(string $separator): self
    {
        $this->innerField->setThousandsSeparator($separator);

        return $this;
    }

    public function setDecimalSeparator(string $separator): self
    {
        $this->innerField->setDecimalSeparator($separator);

        return $this;
    }
}
