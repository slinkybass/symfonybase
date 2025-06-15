<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField as EasyField;

class TextareaField
{
    use FieldTrait;

    public const OPTION_PLUGIN = 'data-textarea-field';

    public const OPTION_MAX_HEIGHT = 'data-textarea-max-height';
    public const OPTION_ROWS = 'rows';

    public const OPTION_RESIZEABLE = 'resizeable';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);
        $instance->field->getAsDto()->setAssets(new AssetsDto());

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-textarea')->onlyOnForms())
            ->plugin(true)
            ->setRows(5)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($val));

        return $this;
    }

    public function setMaxHeight(string $val): self
    {
        $this->setHtmlAttribute(self::OPTION_MAX_HEIGHT, $val);

        return $this;
    }

    public function resizeable(bool $val = true): self
    {
        $this->setCustomOption(self::OPTION_RESIZEABLE, $val);

        return $this;
    }

    public function setRows(int $rows): self
    {
        $this->setHtmlAttribute(self::OPTION_ROWS, $rows);

        return $this;
    }
}
