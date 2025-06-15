<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField as EasyField;

class HiddenField
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
