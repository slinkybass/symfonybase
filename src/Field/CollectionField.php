<?php

namespace App\Field;

use App\Field\Trait\CollectionFieldTrait;
use App\Field\Trait\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * EasyAdmin `CollectionField` wrapper.
 *
 * JS: patched fork via {@see CollectionFieldTrait::enablePlugin()} (not stock EA).
 */
class CollectionField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use CollectionFieldTrait;

    private EasyField $innerField;

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
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

    public function allowAdd(bool $allow = true): self
    {
        $this->innerField->allowAdd($allow);

        return $this;
    }

    public function allowDelete(bool $allow = true): self
    {
        $this->innerField->allowDelete($allow);

        return $this;
    }

    public function isEntryComplex(bool $isComplex = true): self
    {
        $this->innerField->setEntryIsComplex($isComplex);

        return $this;
    }

    public function setEntryType(string $formType): self
    {
        $this->innerField->setEntryType($formType);

        return $this;
    }

    public function setEntryToStringMethod(string|callable $toStringMethod): self
    {
        $this->innerField->setEntryToStringMethod($toStringMethod);

        return $this;
    }

    public function showEntryLabel(bool $show = true): self
    {
        $this->innerField->showEntryLabel($show);

        return $this;
    }

    public function isExpanded(bool $expanded = true): self
    {
        $this->innerField->renderExpanded($expanded);

        return $this;
    }

    public function useEntryCrudForm(?string $crudController = null, ?string $pageNameNew = null, ?string $pageNameEdit = null): self
    {
        $this->innerField->useEntryCrudForm($crudController, $pageNameNew, $pageNameEdit);

        return $this;
    }
}
