<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField as EasyField;

class MoneyField
{
    use FieldTrait;

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->setCurrency('EUR')
            ->storedAsCents(false)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function setCurrency(string $currency): self
    {
        $this->field->setCurrency($currency);

        return $this;
    }

    public function setCurrencyPropertyPath(string $currency): self
    {
        $this->field->setCurrencyPropertyPath($currency);

        return $this;
    }

    public function setDecimals(int $decimals): self
    {
        $this->field->setNumDecimals($decimals);

        return $this;
    }

    public function storedAsCents(bool $asCents = true): self
    {
        $this->field->setStoredAsCents($asCents);

        return $this;
    }
}
