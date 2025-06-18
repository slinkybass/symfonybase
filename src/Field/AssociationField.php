<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField as EasyField;

class AssociationField
{
    use FieldTrait;

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->plugin()
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $enable = true): self
    {
        $this->field->renderAsNativeWidget(!$enable);

        return $this;
    }

    public function setCrudController(string $crudController): self
    {
        $this->field->setCrudController($crudController);

        return $this;
    }

    public function setQueryBuilder(\Closure $queryBuilderCallable): self
    {
        $this->field->setQueryBuilder($queryBuilderCallable);

        return $this;
    }

    public function renderAsEmbeddedForm(?string $crudController = null, ?string $pageNameNew = null, ?string $pageNameEdit = null): self
    {
        $this->field->renderAsEmbeddedForm($crudController, $pageNameNew, $pageNameEdit);

        return $this;
    }

    public function setSortProperty(string $property): self
    {
        $this->field->setSortProperty($property);

        return $this;
    }
}
