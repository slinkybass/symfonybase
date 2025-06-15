<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField as EasyField;

class ChoiceField
{
    use FieldTrait;

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);
        $instance->field->getAsDto()->setAssets(new AssetsDto());

        $instance
            ->plugin(true)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $val = true): self
    {
        $this->field->renderAsNativeWidget(!$val);

        return $this;
    }

    public function multiple(bool $val = true): self
    {
        $this->field->allowMultipleChoices($val);

        return $this;
    }

    public function expanded(bool $val = true): self
    {
        $this->field->renderExpanded($val);

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

    public function renderAsBadges(bool $val = true): self
    {
        $this->field->renderAsBadges($val);

        return $this;
    }
}
