<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField as EasyField;

class ChoiceField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public static function new(string $propertyName, $label = null): self
    {
        $field = new self();
        $field->innerField = EasyField::new($propertyName, $label);
        $field->initField($field->innerField);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
        $this->enablePlugin();
    }

    public function enablePlugin(bool $enable = true): self
    {
        $this->innerField->renderAsNativeWidget(!$enable);

        return $this;
    }

    public function isMultiple(bool $multiple = true): self
    {
        $this->innerField->allowMultipleChoices($multiple);

        return $this;
    }

    public function isExpanded(bool $expanded = true): self
    {
        $this->enablePlugin(!$expanded);
        $this->innerField->renderExpanded($expanded);

        return $this;
    }

    public function setChoices($choices): self
    {
        $this->innerField->setChoices($choices);

        return $this;
    }

    public function setTransChoices($choices): self
    {
        $this->innerField->setTranslatableChoices($choices);

        return $this;
    }

    public function renderAsBadges(bool $badges = true): self
    {
        $this->innerField->renderAsBadges($badges);

        return $this;
    }
}
