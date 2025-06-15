<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField as EasyField;

class TelephoneField
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
}
