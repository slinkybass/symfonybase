<?php

namespace App\Field;

use App\Field\Trait\AceFieldTrait;
use App\Field\Trait\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `CodeEditorField` wrapper with Ace plugin options (`AceFieldTrait` / `lib/ace.js`). */
class CodeEditorField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use AceFieldTrait;

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
