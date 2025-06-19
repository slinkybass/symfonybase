<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\FormField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

class FormField
{
    use FieldTrait;

    private EasyField $field;

    public static function tab(TranslatableInterface|string|false|null $label = null, ?string $icon = null, ?string $propertySuffix = null): self
    {
        $instance = new self();
        $instance->field = EasyField::addTab($label, $icon, $propertySuffix);

        $instance
            ->setDefaultColumns(12);

        return $instance;
    }

    public static function panel($label = false, ?string $icon = null): self
    {
        $instance = new self();
        $instance->field = EasyField::addPanel($label, $icon);

        $instance
            ->setDefaultColumns(12);

        return $instance;
    }

    public static function row(string $breakpoint = '', ?string $propertySuffix = null): self
    {
        $instance = new self();
        $instance->field = EasyField::addRow($breakpoint, $propertySuffix);

        $instance
            ->setDefaultColumns(12);

        return $instance;
    }

    public static function col(int|string $cols = 'col', TranslatableInterface|string|false|null $label = null, ?string $icon = null, ?string $help = null, ?string $propertySuffix = null): self
    {
        $instance = new self();
        $instance->field = EasyField::addColumn($cols, $label, $icon, $help, $propertySuffix);

        $instance
            ->setDefaultColumns(12);

        return $instance;
    }

    public static function fieldset($label = false, ?string $icon = null, ?string $propertySuffix = null): self
    {
        $instance = new self();
        $instance->field = EasyField::addFieldset($label, $icon, $propertySuffix);

        $instance
            ->setDefaultColumns(12);

        return $instance;
    }

    public function setIcon(string $icon): self
    {
        $this->field->setIcon($icon);

        return $this;
    }

    public function collapsible(bool $collapsible = true): self
    {
        $this->field->collapsible($collapsible);

        return $this;
    }

    public function collapsed(bool $collapsed = true): self
    {
        $this->field->renderCollapsed($collapsed);

        return $this;
    }
}
