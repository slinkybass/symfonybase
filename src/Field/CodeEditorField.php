<?php

namespace App\Field;

use App\Field\Trait\CodeEditorFieldTrait;
use App\Field\Trait\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `CodeEditorField` wrapper with Ace plugin options. */
class CodeEditorField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use CodeEditorFieldTrait;

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

    /** Load Ace (`data-codeeditor-field`). `false` keeps a plain textarea. */
    public function enablePlugin(bool $enable = true): self
    {
        return $this->enableMappedPlugin('form-type-codeeditor', self::OPTION_PLUGIN, $enable);
    }
}
