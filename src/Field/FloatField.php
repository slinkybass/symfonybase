<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\NoUiSliderFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `NumberField` wrapper (float/decimal) with optional noUiSlider. */
class FloatField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use NoUiSliderFieldTrait;

    private EasyField $innerField;

    public const OPTION_HTML5 = 'html5';
    public const OPTION_MAX = 'max';
    public const OPTION_MIN = 'min';
    public const OPTION_STEP = 'step';

    public const DEFAULT_DECIMALS = 2;
    public const DEFAULT_STEP = 0.1;
    public const DEFAULT_HTML5 = true;

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        $field = new self();
        $field->innerField = EasyField::new($propertyName, $label);
        $field->initField($field->innerField);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
        $this->setDecimals(self::DEFAULT_DECIMALS);
        $this->setStep(self::DEFAULT_STEP);
        $this->setFormTypeOption(self::OPTION_HTML5, self::DEFAULT_HTML5);
    }

    public function setMax(int|float|null $max): self
    {
        $this->setHtmlAttribute(self::OPTION_MAX, $max);

        return $this;
    }

    public function setMin(int|float|null $min): self
    {
        $this->setHtmlAttribute(self::OPTION_MIN, $min);

        return $this;
    }

    public function setStep(int|float|null $step): self
    {
        $this->setHtmlAttribute(self::OPTION_STEP, $step);

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
