<?php

namespace App\Field\Trait;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;

/**
 * Shared asset wiring for `CollectionField` / `ArrayField`.
 *
 * Replaces EasyAdmin `field-collection.js` with the patched fork
 * `assets/js/fields/form-type-collection.js` (Tabler empty-state, hierarchy
 * `data-hf-*` uniquify, no prototype `eval`). Markup and events stay EA:
 * `data-ea-collection-field` (Twig `collection_row`), `ea.collection.*`.
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
        $this->dto->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new(self::COLLECTION_PLUGIN_ENTRY)->onlyOnForms());
        }

        return $this;
    }
}
