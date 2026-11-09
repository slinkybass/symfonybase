<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\TinyMceFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `TextEditorField` wrapper with TinyMCE plugin options (`TinyMceFieldTrait` / `lib/tinymce.js`). */
class TextEditorField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use TinyMceFieldTrait;

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
