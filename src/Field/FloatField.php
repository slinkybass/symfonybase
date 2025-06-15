<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField as EasyField;

class FloatField
{
    use FieldTrait;

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->setDecimals(2)
            ->setFormTypeOption('html5', true)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function setDecimals(int $num): self
    {
        $this->field->setNumDecimals($num);

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

    public function setNumberFormat(string $sprintfFormat): self
    {
        $this->field->setNumberFormat($sprintfFormat);

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
