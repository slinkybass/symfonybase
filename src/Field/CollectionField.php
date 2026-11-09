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
 * Options and JS fork: {@see CollectionFieldTrait}.
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
}
