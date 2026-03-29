<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField as EasyField;

class PercentField implements FieldInterface
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
            ->storedAsFractional(false);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
    }

    public function setDecimals(int $decimals): self
    {
        $this->innerField->setNumDecimals($decimals);

        return $this;
    }

    public function storedAsFractional(bool $isFractional = true): self
    {
        $this->innerField->setStoredAsFractional($isFractional);

        return $this;
    }

    public function setSymbol(?string $symbol): self
    {
        $this->innerField->setSymbol($symbol);

        return $this;
    }

    public function setRoundingMode(int $mode): self
    {
        $this->innerField->setRoundingMode($mode);

        return $this;
    }
}
