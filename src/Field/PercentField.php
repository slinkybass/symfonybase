<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `PercentField` wrapper with `FieldTrait`. */
class PercentField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const DEFAULT_STORED_AS_FRACTIONAL = false;
    public const DEFAULT_STEP = 1;
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
        $this->storedAsFractional(self::DEFAULT_STORED_AS_FRACTIONAL);
        $this->setStep(self::DEFAULT_STEP);
        $this->setFormTypeOption(FloatField::OPTION_HTML5, self::DEFAULT_HTML5);
    }

    /** Highest allowed value (`max`). */
    public function setMax(int|float|null $max): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_MAX, $max);

        return $this;
    }

    /** Lowest allowed value (`min`). */
    public function setMin(int|float|null $min): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_MIN, $min);

        return $this;
    }

    /** Increment of the number input (`step`). Default `1` (matches EA `numDecimals` `0`). */
    public function setStep(int|float|null $step): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_STEP, $step);

        return $this;
    }

    /** Decimal places shown. Pair with `setStep()` when allowing fractions (e.g. `setDecimals(2)->setStep(0.01)`). */
    public function setDecimals(int $decimals): self
    {
        $this->innerField->setNumDecimals($decimals);

        return $this;
    }

    /** Store a fraction (`0.15` = 15%). Default stores the percent number (`15` = 15%). */
    public function storedAsFractional(bool $isFractional = true): self
    {
        $this->innerField->setStoredAsFractional($isFractional);

        return $this;
    }

    /** Symbol shown beside the value. Default `%`. Pass `null` to hide it. */
    public function setSymbol(?string $symbol): self
    {
        $this->innerField->setSymbol($symbol);

        return $this;
    }

    /** ICU rounding mode (`NumberFormatter::ROUND_*`). */
    public function setRoundingMode(int $mode): self
    {
        $this->innerField->setRoundingMode($mode);

        return $this;
    }
}
