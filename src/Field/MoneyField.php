<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField as EasyField;

class MoneyField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public static function new(string $propertyName, $label = null): self
    {
        $field = new self();
        $field->innerField = EasyField::new($propertyName, $label);
        $field->initField($field->innerField);
        $field
            ->setCurrency('EUR')
            ->storedAsCents(false);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
    }

    public function setCurrency(string $currency): self
    {
        $this->innerField->setCurrency($currency);

        return $this;
    }

    public function setCurrencyPropertyPath(string $currency): self
    {
        $this->innerField->setCurrencyPropertyPath($currency);

        return $this;
    }

    public function setDecimals(int $decimals): self
    {
        $this->innerField->setNumDecimals($decimals);

        return $this;
    }

    public function storedAsCents(bool $asCents = true): self
    {
        $this->innerField->setStoredAsCents($asCents);

        return $this;
    }
}
