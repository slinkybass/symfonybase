<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField as EasyField;

class BooleanField
{
    use FieldTrait;

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->switch(false)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function switch(bool $val = true): self
    {
        $this->field->renderAsSwitch($val);

        return $this;
    }

    public function hideTrue(bool $val = true): self
    {
        $this->field->hideValueWhenTrue($val);

        return $this;
    }

    public function hideFalse(bool $val = true): self
    {
        $this->field->hideValueWhenFalse($val);

        return $this;
    }
}
