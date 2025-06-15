<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField as EasyField;

class ChoiceField
{
    use FieldTrait;

    public const OPTION_WIDGET = 'widget';
    public const WIDGET_AUTOCOMPLETE = 'autocomplete';
    public const WIDGET_NATIVE = 'native';

    public const OPTION_ALLOW_MULTIPLE_CHOICES = 'allowMultipleChoices';
    public const OPTION_RENDER_EXPANDED = 'renderExpanded';
    public const OPTION_CHOICES = 'choices';
    public const OPTION_USE_TRANSLATABLE_CHOICES = 'useTranslatableChoices';
    public const OPTION_RENDER_AS_BADGES = 'renderAsBadges';

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
        $this->setCustomOption(self::OPTION_WIDGET, $val ? self::WIDGET_AUTOCOMPLETE : self::WIDGET_NATIVE);

        return $this;
    }

    public function multiple(bool $allow = true): self
    {
        $this->setCustomOption(self::OPTION_ALLOW_MULTIPLE_CHOICES, $allow);

        return $this;
    }

    public function expanded(bool $expanded = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_EXPANDED, $expanded);

        return $this;
    }

    public function setChoices($choiceGenerator): self
    {
        $this->setCustomOption(self::OPTION_CHOICES, $choiceGenerator);

        return $this;
    }

    public function setTransChoices($choiceGenerator): self
    {
        $this->setChoices($choiceGenerator);
        $this->setCustomOption(self::OPTION_USE_TRANSLATABLE_CHOICES, true);

        return $this;
    }

    public function renderAsBadges($badgeSelector = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_AS_BADGES, $badgeSelector);

        return $this;
    }
}
