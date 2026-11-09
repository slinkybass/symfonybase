<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * Layout primitives (fieldset/panel, row, column, tab) delegating to EasyAdmin `FormField`; use the named static factories — `::new()` is intentionally unusable.
 */
class FormField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    /**
     * Unusable. Layout fields are created with `fieldset()`, `row()`, `col()`, or `tab()`.
     */
    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): never
    {
        EasyField::new($propertyName, $label);

        throw new \LogicException('Use fieldset(), row(), col(), or tab() instead of FormField::new().');
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

    /** Icon on the tab or panel (Font Awesome class, e.g. `fa fa-user`). */
    public function setIcon(string $icon): self
    {
        $this->innerField->setIcon($icon);

        return $this;
    }

    /** Allow the panel to be collapsed. */
    public function isCollapsible(bool $collapsible = true): self
    {
        $this->innerField->collapsible($collapsible);

        return $this;
    }

    /** Render the panel collapsed until the user opens it. */
    public function isCollapsed(bool $collapsed = true): self
    {
        $this->innerField->renderCollapsed($collapsed);

        return $this;
    }
}
