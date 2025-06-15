<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField as EasyField;

class ChoiceField
{
    use FieldTrait;

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->plugin()
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $enable = true): self
    {
        $this->field->renderAsNativeWidget(!$enable);

        return $this;
    }

    public function multiple(bool $multiple = true): self
    {
        $this->field->allowMultipleChoices($multiple);

        return $this;
    }

    public function expanded(bool $expanded = true): self
    {
        $this->field->renderAsNativeWidget($expanded);
        $this->field->renderExpanded($expanded);

        return $this;
    }

    public function setChoices($choices): self
    {
        $this->field->setChoices($choices);

        return $this;
    }

    public function setTransChoices($choices): self
    {
        $this->field->setTranslatableChoices($choices);

        return $this;
    }

    public function renderAsBadges(bool $badges = true): self
    {
        $this->field->renderAsBadges($badges);

        return $this;
    }
}
