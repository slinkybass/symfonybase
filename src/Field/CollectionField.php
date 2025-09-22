<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField as EasyField;

class CollectionField
{
    use FieldTrait;

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);
        $instance->field->getAsDto()->setAssets(new AssetsDto());

        $instance
            ->setTemplatePath('field/extendedMany.html.twig')
            ->addAssetMapperEntries(Asset::new('form-type-collection')->onlyOnForms())
            ->setDefaultColumns(12);

        return $instance;
    }

    public function allowAdd(bool $allow = true): self
    {
        $this->field->allowAdd($allow);

        return $this;
    }

    public function allowDelete(bool $allow = true): self
    {
        $this->field->allowDelete($allow);

        return $this;
    }

    public function isEntryComplex(bool $isComplex = true): self
    {
        $this->field->setEntryIsComplex($isComplex);

        return $this;
    }

    public function setEntryType(string $formType): self
    {
        $this->field->setEntryType($formType);

        return $this;
    }

    public function setEntryToStringMethod(string|callable $toStringMethod): self
    {
        $this->field->setEntryToStringMethod($toStringMethod);

        return $this;
    }

    public function showEntryLabel(bool $show = true): self
    {
        $this->field->showEntryLabel($show);

        return $this;
    }

    public function isExpanded(bool $expanded = true): self
    {
        $this->field->renderExpanded($expanded);

        return $this;
    }

    public function useEntryCrudForm(?string $crudController = null, ?string $pageNameNew = null, ?string $pageNameEdit = null): self
    {
        $this->field->useEntryCrudForm($crudController, $pageNameNew, $pageNameEdit);

        return $this;
    }
}
