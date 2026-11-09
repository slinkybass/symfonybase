<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `BooleanField` wrapper with `FieldTrait` (checkbox or switch styling). */
class BooleanField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const OPTION_CHECKED = 'checked';
    public const DEFAULT_SWITCH = false;

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
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

    /** Pre-check the widget (`checked`). The stored value still comes from the entity. */
    public function isChecked(bool $checked = true): self
    {
        $this->setHtmlAttribute(self::OPTION_CHECKED, $checked);

        return $this;
    }

    /** Render a toggle switch. `false` keeps a checkbox. */
    public function isSwitch(bool $enabled = true): self
    {
        $this->innerField->renderAsSwitch($enabled);

        return $this;
    }

    /** On index, hide the value when it is true. */
    public function isHiddenOnTrue(bool $hidden = true): self
    {
        $this->innerField->hideValueWhenTrue($hidden);

        return $this;
    }

    /** On index, hide the value when it is false. */
    public function isHiddenOnFalse(bool $hidden = true): self
    {
        $this->innerField->hideValueWhenFalse($hidden);

        return $this;
    }
}
