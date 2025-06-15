<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField as EasyField;

class PercentField
{
    use FieldTrait;

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->storedAsFractional(false)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function setDecimals(int $decimals): self
    {
        $this->field->setNumDecimals($decimals);

        return $this;
    }

    public function storedAsFractional(bool $isFractional = true): self
    {
        $this->field->setStoredAsFractional($isFractional);

        return $this;
    }

    public function setSymbol(?string $symbol): self
    {
        $this->field->setSymbol($symbol);

        return $this;
    }

    public function setRoundingMode(int $mode): self
    {
        $this->field->setRoundingMode($mode);

        return $this;
    }
}
