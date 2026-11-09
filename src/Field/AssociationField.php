<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\TomSelectFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `AssociationField` wrapper with `FieldTrait` and TomSelect options (`TomSelectFieldTrait`). */
class AssociationField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use TomSelectFieldTrait;

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

    /** CRUD controller of the related entity, used for links and autocomplete. */
    public function setCrudController(string $crudController): self
    {
        $this->innerField->setCrudController($crudController);

        return $this;
    }

    /**
     * Load options over AJAX instead of rendering every related entity.
     *
     * @param callable|null $callback Optional result mapper `(Entity $entity): array`
     */
    public function autocomplete(bool $enable = true, ?callable $callback = null, ?string $template = null, bool $renderAsHtml = false): self
    {
        $this->innerField->autocomplete($enable, $callback, $template, $renderAsHtml);

        return $this;
    }

    /** Filter or sort the options. The closure receives the association QueryBuilder (alias `entity`). */
    public function setQueryBuilder(\Closure $queryBuilderCallable): self
    {
        $this->innerField->setQueryBuilder($queryBuilderCallable);

        return $this;
    }

    /** Edit the related entity inline on this form instead of showing a select. */
    public function renderAsEmbeddedForm(?string $crudController = null, ?string $pageNameNew = null, ?string $pageNameEdit = null): self
    {
        $this->innerField->renderAsEmbeddedForm($crudController, $pageNameNew, $pageNameEdit);

        return $this;
    }

    /** Property used to order the options. */
    public function setSortProperty(string $property): self
    {
        $this->innerField->setSortProperty($property);

        return $this;
    }
}
