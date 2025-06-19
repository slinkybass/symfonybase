<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\FormField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

class FormField
{
    use FieldTrait;

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->setDefaultColumns(12);

        return $instance;
    }

    public function tab(TranslatableInterface|string|false|null $label = null, ?string $icon = null, ?string $propertySuffix = null): self
    {
        $this->field->addTab($label, $icon, $propertySuffix);

        return $this;
    }

    public function panel($label = false, ?string $icon = null): self
    {
        $this->field->addPanel($label, $icon);

        return $this;
    }

    public function row(string $breakpoint = '', ?string $propertySuffix = null): self
    {
        $this->field->addRow($breakpoint, $propertySuffix);

        return $this;
    }

    public function col(int|string $cols = 'col', TranslatableInterface|string|false|null $label = null, ?string $icon = null, ?string $help = null, ?string $propertySuffix = null): self
    {
        $this->field->addColumn($cols, $label, $icon, $help, $propertySuffix);

        return $this;
    }

    public function fieldset($label = false, ?string $icon = null, ?string $propertySuffix = null): self
    {
        $this->field->addFieldset($label, $icon, $propertySuffix);

        return $this;
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

    public function renderCollapsed(bool $collapsed = true): self
    {
        $this->field->renderCollapsed($collapsed);

        return $this;
    }
}
