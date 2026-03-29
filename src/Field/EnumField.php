<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField as EasyField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class EnumField implements FieldInterface
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
        $this->plugin();
        $this->setFormType(EnumType::class);
        $this->setFormTypeOption('choice_label', fn($e) => $e->translationKey());
    }

    public function plugin(bool $enable = true): self
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
        $this->plugin(!$expanded);
        $this->innerField->renderExpanded($expanded);

        return $this;
    }

    public function renderAsBadges(bool $badges = true): self
    {
        $this->innerField->renderAsBadges($badges);

        return $this;
    }
}
