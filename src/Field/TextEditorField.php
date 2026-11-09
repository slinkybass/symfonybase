<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\TextEditorFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `TextEditorField` wrapper with TinyMCE plugin options. */
class TextEditorField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use TextEditorFieldTrait;

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

    /** Load TinyMCE (`data-texteditor-field`). `false` keeps a plain textarea. */
    public function enablePlugin(bool $enable = true): self
    {
        return $this->enableMappedPlugin('form-type-texteditor', self::OPTION_PLUGIN, $enable);
    }
}
