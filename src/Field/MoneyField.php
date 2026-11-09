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

    /** Decimal places shown. */
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
