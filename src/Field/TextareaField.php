<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField as EasyField;

class TextareaField
{
    use FieldTrait;

    public const OPTION_PLUGIN = 'data-textarea-field';

    public const OPTION_TEXTAREA_MAX_HEIGHT = 'data-textarea-max-height';

    public const OPTION_RESIZEABLE = 'resizeable';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);
        $instance->field->getAsDto()->setAssets(new AssetsDto());

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-textarea')->onlyOnForms())
            ->plugin()
            ->setRows(5)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $enable = true): self
    {
        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($enable));

        return $this;
    }

    public function setMaxHeight(string $maxHeight): self
    {
        $this->setHtmlAttribute(self::OPTION_TEXTAREA_MAX_HEIGHT, $maxHeight);

        return $this;
    }

    public function isResizeable(bool $resizeable = true): self
    {
        $this->setCustomOption(self::OPTION_RESIZEABLE, $resizeable);

        return $this;
    }

    public function setRows(int $rows): self
    {
        $this->field->setNumOfRows($rows);

        return $this;
    }
}
