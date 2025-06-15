<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField as EasyField;

class CodeEditorField
{
    use FieldTrait;

    public const OPTION_PLUGIN = 'data-codeeditor-field';

    public const OPTION_CODEEDITOR_THEME = 'data-codeeditor-theme';
    public const OPTION_CODEEDITOR_LANGUAGE = 'data-codeeditor-language';
    public const OPTION_CODEEDITOR_TAB_SIZE = 'data-codeeditor-tab-size';
    public const OPTION_CODEEDITOR_INDENT_WITH_TABS = 'data-codeeditor-indent-with-tabs';
    public const OPTION_CODEEDITOR_SHOW_LINE_NUMBERS = 'data-codeeditor-show-line-numbers';
    public const OPTION_CODEEDITOR_MIN_LINES = 'data-codeeditor-min-lines';
    public const OPTION_CODEEDITOR_MAX_LINES = 'data-codeeditor-max-lines';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);
        $instance->field->getAsDto()->setAssets(new AssetsDto());

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-codeeditor')->onlyOnForms())
            ->plugin(true)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($val));

        return $this;
    }

    public function setTheme(string $val): self
    {
        $this->setHtmlAttribute(self::OPTION_CODEEDITOR_THEME, $val);

        return $this;
    }

    public function setLanguage(string $val): self
    {
        $this->setHtmlAttribute(self::OPTION_CODEEDITOR_LANGUAGE, $val);

        return $this;
    }

    public function setTabSize(int $val): self
    {
        $this->setHtmlAttribute(self::OPTION_CODEEDITOR_TAB_SIZE, $val);

        return $this;
    }

    public function indentWithTabs(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_CODEEDITOR_INDENT_WITH_TABS, json_encode($val));

        return $this;
    }

    public function showLineNumbers(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_CODEEDITOR_SHOW_LINE_NUMBERS, json_encode($val));

        return $this;
    }

    public function setMinLines(int $val): self
    {
        $this->setHtmlAttribute(self::OPTION_CODEEDITOR_MIN_LINES, $val);

        return $this;
    }

    public function setMaxLines(int $val): self
    {
        $this->setHtmlAttribute(self::OPTION_CODEEDITOR_MAX_LINES, $val);

        return $this;
    }
}
