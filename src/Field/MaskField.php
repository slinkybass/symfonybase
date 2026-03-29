<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField as EasyField;

class MaskField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const OPTION_PLUGIN = 'data-mask-field';

    public const OPTION_MASK_REGEX = 'data-mask-regex';
    public const OPTION_MASK_PATTERN = 'data-mask-pattern';
    public const OPTION_MASK_OVERWRITE = 'data-mask-overwrite';
    public const OPTION_MASK_PLACEHOLDER = 'data-mask-placeholder';

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
    }

    public function plugin(bool $enable = true): self
    {
        $this->dto->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new('form-type-mask')->onlyOnForms());
        }
        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($enable));

        return $this;
    }

    public function isRegex(bool $regex = true): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_REGEX, json_encode($regex));

        return $this;
    }

    public function setPattern(string $pattern): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_PATTERN, $pattern);

        return $this;
    }

    public function isOverwrite(bool $overwrite = true): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_OVERWRITE, json_encode($overwrite));

        return $this;
    }

    public function setMaskPlaceholder(string $placeholder): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_PLACEHOLDER, $placeholder);

        return $this;
    }
}
