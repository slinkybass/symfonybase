<?php

namespace App\Field;

use App\Field\Trait\CollectionFieldTrait;
use App\Field\Trait\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * EasyAdmin `ArrayField` wrapper.
 *
 * Same collection fork and shared options as {@see CollectionField}
 * ({@see CollectionFieldTrait}: `enablePlugin`, `allowAdd`, `allowDelete`, …).
 * Entry-type / CRUD-form options are Collection-oriented; prefer {@see CollectionField} for those.
 */
class ArrayField implements FieldInterface
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
}
