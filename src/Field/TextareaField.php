<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField as EasyField;

class TextareaField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const OPTION_PLUGIN = 'data-textarea-field';

    public const OPTION_TEXTAREA_MAX_HEIGHT = 'data-textarea-max-height';

    public const OPTION_RESIZEABLE = 'resizeable';

    public static function new(string $propertyName, $label = null): self
    {
        $field = new self();
        $field->innerField = EasyField::new($propertyName, $label);
        $field->initField($field->innerField);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
        $this->plugin();
        $this->setRows(5);
    }

    public function plugin(bool $enable = true): self
    {
        $this->dto->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new('form-type-textarea')->onlyOnForms());
        }
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
        $this->innerField->setNumOfRows($rows);

        return $this;
    }
}
