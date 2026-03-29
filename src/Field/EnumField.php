<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField as EasyField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class EnumField
{
    use FieldTrait;

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->plugin()
            ->setFormType(EnumType::class)
            ->setFormTypeOption('choice_label', fn($e) => $e->translationKey())
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $enable = true): self
    {
        $this->field->renderAsNativeWidget(!$enable);

        return $this;
    }

    public function isMultiple(bool $multiple = true): self
    {
        $this->field->allowMultipleChoices($multiple);

        return $this;
    }

    public function isExpanded(bool $expanded = true): self
    {
        $this->field->renderAsNativeWidget($expanded);
        $this->field->renderExpanded($expanded);

        return $this;
    }

    public function renderAsBadges(bool $badges = true): self
    {
        $this->field->renderAsBadges($badges);

        return $this;
    }
}
