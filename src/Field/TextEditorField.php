<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField as EasyField;

class TextEditorField
{
    use FieldTrait;

    public const OPTION_PLUGIN = 'data-texteditor-field';

    public const OPTION_TEXTEDITOR_RESIZE = 'data-texteditor-resize';
    public const OPTION_TEXTEDITOR_SPELLCHECK = 'data-texteditor-spellcheck';
    public const OPTION_TEXTEDITOR_TOOLBAR = 'data-texteditor-toolbar';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);
        $instance->field->getAsDto()->setAssets(new AssetsDto());

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-texteditor')->onlyOnForms())
            ->plugin(true)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($val));

        return $this;
    }

    public function resize(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_TEXTEDITOR_RESIZE, json_encode($val));

        return $this;
    }

    public function spellcheck(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_TEXTEDITOR_SPELLCHECK, json_encode($val));

        return $this;
    }

    public function setToolbar(string $val): self
    {
        $this->setHtmlAttribute(self::OPTION_TEXTEDITOR_TOOLBAR, $val);

        return $this;
    }
}
