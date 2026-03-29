<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField as EasyField;

class BooleanField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const OPTION_CHECKED = 'checked';

    public static function new(string $propertyName, ?string $label = null): self
    {
        $field = new self();
        $field->innerField = EasyField::new($propertyName, $label);
        $field->initField($field->innerField);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
        $this->isSwitch(false);
    }

    public function isChecked(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_CHECKED, $val);

        return $this;
    }

    public function isSwitch(bool $val = true): self
    {
        $this->innerField->renderAsSwitch($val);

        return $this;
    }

    public function isHiddenOnTrue(bool $val = true): self
    {
        $this->innerField->hideValueWhenTrue($val);

        return $this;
    }

    public function isHiddenOnFalse(bool $val = true): self
    {
        $this->innerField->hideValueWhenFalse($val);

        return $this;
    }
}
