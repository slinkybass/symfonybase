<?php

namespace App\Field\Trait;

use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField as EasyCollectionField;

/**
 * Shared collection/array logic for `CollectionField` / `ArrayField`.
 *
 * Assets: replaces EasyAdmin `field-collection.js` with the patched fork
 * `assets/js/fields/form-type-collection.js` (Tabler empty-state, hierarchy
 * `data-hf-*` uniquify, no prototype `eval`). Markup and events stay EA:
 * `data-ea-collection-field` (Twig `collection_row`), `ea.collection.*`.
 *
 * Options: EasyAdmin `CollectionField` custom options (also wire `allow_add` /
 * `allow_delete` as form type options so `ArrayField` can use them — its
 * configurator only reads form options).
 *
 * No `data-*` plugin flag: the form theme owns the EA collection contract.
 *
 * Used with {@see FieldTrait} (EasyAdmin private `$dto` composed into the field class).
 *
 * @property FieldDto $dto
 */
trait CollectionFieldTrait
{
    public const COLLECTION_PLUGIN_ENTRY = 'form-type-collection';

    /**
     * Load the collection fork (`form-type-collection`). Always clears EA assets.
     * `false` loads nothing (add/delete buttons will not work with the Tabler theme).
     */
    public function enablePlugin(bool $enable = true): self
    {
        return $this->enableMappedPlugin(self::COLLECTION_PLUGIN_ENTRY, null, $enable);
    }

    /** Allow adding new entries (`allow_add`). Default `true`. */
    public function allowAdd(bool $allow = true): self
    {
        $this->setCustomOption(EasyCollectionField::OPTION_ALLOW_ADD, $allow);
        $this->setFormTypeOption('allow_add', $allow);

        return $this;
    }

    /** Allow removing entries (`allow_delete`). Default `true`. */
    public function allowDelete(bool $allow = true): self
    {
        $this->setCustomOption(EasyCollectionField::OPTION_ALLOW_DELETE, $allow);
        $this->setFormTypeOption('allow_delete', $allow);

        return $this;
    }

    /**
     * Complex multi-field entries get accordion rendering (`entryIsComplex`).
     * When unset, EasyAdmin infers it from `setEntryType()`.
     */
    public function isEntryComplex(bool $isComplex = true): self
    {
        $this->setCustomOption(EasyCollectionField::OPTION_ENTRY_IS_COMPLEX, $isComplex);

        return $this;
    }

    /** Symfony form type FQCN for each entry (`entry_type`). */
    public function setEntryType(string $formTypeFqcn): self
    {
        $this->setCustomOption(EasyCollectionField::OPTION_ENTRY_TYPE, $formTypeFqcn);

        return $this;
    }

    /**
     * String used as accordion title / formatted value for an entry.
     *
     * @param string|callable $toStringMethod method name on the entry object, or callable(value, translator)
     */
    public function setEntryToStringMethod(string|callable $toStringMethod): self
    {
        $this->setCustomOption(EasyCollectionField::OPTION_ENTRY_TO_STRING_METHOD, $toStringMethod);

        return $this;
    }

    /** Show the Symfony label on each entry (`entry_options.label`). Default `false`. */
    public function showEntryLabel(bool $show = true): self
    {
        $this->setCustomOption(EasyCollectionField::OPTION_SHOW_ENTRY_LABEL, $show);

        return $this;
    }

    /** Render accordion items expanded (`renderExpanded`). Default `false`. */
    public function isExpanded(bool $expanded = true): self
    {
        $this->setCustomOption(EasyCollectionField::OPTION_RENDER_EXPANDED, $expanded);

        return $this;
    }

    /**
     * Build each entry from another CRUD controller form instead of `setEntryType()`.
     *
     * @param class-string|null $crudControllerFqcn
     */
    public function useEntryCrudForm(?string $crudControllerFqcn = null, ?string $pageNameNew = null, ?string $pageNameEdit = null): self
    {
        $this->setCustomOption(EasyCollectionField::OPTION_ENTRY_USES_CRUD_FORM, true);
        $this->setCustomOption(EasyCollectionField::OPTION_ENTRY_CRUD_CONTROLLER_FQCN, $crudControllerFqcn);
        $this->setCustomOption(EasyCollectionField::OPTION_ENTRY_CRUD_NEW_PAGE_NAME, $pageNameNew);
        $this->setCustomOption(EasyCollectionField::OPTION_ENTRY_CRUD_EDIT_PAGE_NAME, $pageNameEdit);

        return $this;
    }
}
