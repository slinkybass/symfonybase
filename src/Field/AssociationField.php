<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField as EasyField;

class AssociationField implements FieldInterface
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
        $this->enablePlugin();
    }

    public function enablePlugin(bool $enable = true): self
    {
        $this->innerField->renderAsNativeWidget(!$enable);

        return $this;
    }

    public function setCrudController(string $crudController): self
    {
        $this->innerField->setCrudController($crudController);

        return $this;
    }

    public function setQueryBuilder(\Closure $queryBuilderCallable): self
    {
        $this->innerField->setQueryBuilder($queryBuilderCallable);

        return $this;
    }

    public function renderAsEmbeddedForm(?string $crudController = null, ?string $pageNameNew = null, ?string $pageNameEdit = null): self
    {
        $this->innerField->renderAsEmbeddedForm($crudController, $pageNameNew, $pageNameEdit);

        return $this;
    }

    public function setSortProperty(string $property): self
    {
        $this->innerField->setSortProperty($property);

        return $this;
    }
}
