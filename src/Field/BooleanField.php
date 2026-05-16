<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField as EasyField;

/** EasyAdmin `BooleanField` wrapper with `FieldTrait` (checkbox or switch styling). */
class BooleanField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const OPTION_CHECKED = 'checked';
    public const DEFAULT_SWITCH = false;

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
        $this->isSwitch(self::DEFAULT_SWITCH);
    }

    public function isChecked(bool $checked = true): self
    {
        $this->setHtmlAttribute(self::OPTION_CHECKED, $checked);

        return $this;
    }

    public function isSwitch(bool $enabled = true): self
    {
        $this->innerField->renderAsSwitch($enabled);

        return $this;
    }

    public function isHiddenOnTrue(bool $hidden = true): self
    {
        $this->innerField->hideValueWhenTrue($hidden);

        return $this;
    }

    public function isHiddenOnFalse(bool $hidden = true): self
    {
        $this->innerField->hideValueWhenFalse($hidden);

        return $this;
    }
}
