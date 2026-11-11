<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `MoneyField` wrapper with `FieldTrait`. */
class MoneyField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const DEFAULT_CURRENCY = 'EUR';
    public const DEFAULT_STORED_AS_CENTS = false;
    public const DEFAULT_STEP = 0.01;
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
        $this->setCurrency(self::DEFAULT_CURRENCY);
        $this->storedAsCents(self::DEFAULT_STORED_AS_CENTS);
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

    /** Increment of the number input (`step`). Default `0.01` (matches EA `numDecimals` `2`). */
    public function setStep(int|float|null $step): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_STEP, $step);

        return $this;
    }

    /** ISO 4217 currency code. Default `EUR`. */
    public function setCurrency(string $currency): self
    {
        $this->innerField->setCurrency($currency);

        return $this;
    }

    /** Entity property that holds this row's currency code. */
    public function setCurrencyPropertyPath(string $currency): self
    {
        $this->innerField->setCurrencyPropertyPath($currency);

        return $this;
    }

    /** Decimal places shown. Pair with `setStep()` when changing scale (e.g. `setDecimals(0)->setStep(1)`). */
    public function setDecimals(int $decimals): self
    {
        $this->innerField->setNumDecimals($decimals);

        return $this;
    }

    /** Store integer minor units (`199` = 1.99). Default stores the major unit (`1.99`). */
    public function storedAsCents(bool $asCents = true): self
    {
        $this->innerField->setStoredAsCents($asCents);

        return $this;
    }
}
