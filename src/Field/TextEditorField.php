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

        $instance
            ->plugin()
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $enable = true): self
    {
        $this->field->getAsDto()->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new('form-type-texteditor')->onlyOnForms());
        }

        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($enable));

        return $this;
    }

    public function isResizeable(bool $resizeable = true): self
    {
        $this->setHtmlAttribute(self::OPTION_TEXTEDITOR_RESIZE, json_encode($resizeable));

        return $this;
    }

    public function isSpellcheck(bool $spellcheck = true): self
    {
        $this->setHtmlAttribute(self::OPTION_TEXTEDITOR_SPELLCHECK, json_encode($spellcheck));

        return $this;
    }

    public function setToolbar(string $toolbar): self
    {
        $this->setHtmlAttribute(self::OPTION_TEXTEDITOR_TOOLBAR, $toolbar);

        return $this;
    }
}
