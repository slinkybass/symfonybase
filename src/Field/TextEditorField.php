<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField as EasyField;

class TextEditorField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const OPTION_PLUGIN = 'data-texteditor-field';

    public const OPTION_TEXTEDITOR_RESIZE = 'data-texteditor-resize';
    public const OPTION_TEXTEDITOR_SPELLCHECK = 'data-texteditor-spellcheck';
    public const OPTION_TEXTEDITOR_TOOLBAR = 'data-texteditor-toolbar';

    public static function new(string $propertyName, $label = null): self
    {
        $field = new self();
        $field->innerField = EasyField::new($propertyName, $label);
        $field->initField($field->innerField);
        $field
            ->plugin();

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
    }

    public function plugin(bool $enable = true): self
    {
        $this->innerField->getAsDto()->setAssets(new AssetsDto());
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
