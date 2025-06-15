<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField as EasyField;

class IntegerField
{
    use FieldTrait;

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->setDefaultColumns(12);

        return $instance;
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
}
