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

    public function switch(bool $switch = true): self
    {
        $this->field->renderAsSwitch($switch);

        return $this;
    }

    public function hideTrue(bool $hide = true): self
    {
        $this->field->hideValueWhenTrue($hide);

        return $this;
    }

    public function hideFalse(bool $hide = true): self
    {
        $this->field->hideValueWhenFalse($hide);

        return $this;
    }
}
