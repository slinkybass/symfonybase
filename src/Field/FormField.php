<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

class FormField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public static function new(string $propertyName, $label = null): never
    {
        EasyField::new($propertyName, $label);
    }

    public static function panel($label = false, ?string $icon = null): self
    {
        return self::fieldset($label, $icon);
    }

    public static function fieldset($label = false, ?string $icon = null, ?string $propertySuffix = null): self
    {
        $field = new self();
        $field->innerField = EasyField::addFieldset($label, $icon, $propertySuffix);
        $field->initField($field->innerField);

        return $field;
    }

    public static function row(string $breakpoint = '', ?string $propertySuffix = null): self
    {
        $field = new self();
        $field->innerField = EasyField::addRow($breakpoint, $propertySuffix);
        $field->initField($field->innerField);

        return $field;
    }

    public static function col(int|string $cols = 'col', TranslatableInterface|string|false|null $label = null, ?string $icon = null, ?string $help = null, ?string $propertySuffix = null): self
    {
        $field = new self();
        $field->innerField = EasyField::addColumn($cols, $label, $icon, $help, $propertySuffix);
        $field->initField($field->innerField);

        return $field;
    }

    public static function tab(TranslatableInterface|string|false|null $label = null, ?string $icon = null, ?string $propertySuffix = null): self
    {
        $field = new self();
        $field->innerField = EasyField::addTab($label, $icon, $propertySuffix);
        $field->initField($field->innerField);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
    }

    public function setIcon(string $icon): self
    {
        $this->innerField->setIcon($icon);

        return $this;
    }

    public function isCollapsible(bool $val = true): self
    {
        $this->innerField->collapsible($val);

        return $this;
    }

    public function isCollapsed(bool $val = true): self
    {
        $this->innerField->renderCollapsed($val);

        return $this;
    }
}
