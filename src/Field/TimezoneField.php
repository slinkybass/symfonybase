<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\TimezoneField as EasyField;

class TimezoneField
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
