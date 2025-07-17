<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField as EasyField;

class MaskField
{
    use FieldTrait;

    public const OPTION_PLUGIN = 'data-mask-field';

    public const OPTION_MASK_REGEX = 'data-mask-regex';
    public const OPTION_MASK_PATTERN = 'data-mask-pattern';
    public const OPTION_MASK_OVERWRITE = 'data-mask-overwrite';
    public const OPTION_MASK_PLACEHOLDER = 'data-mask-placeholder';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-mask')->onlyOnForms())
            ->plugin()
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $enable = true): self
    {
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

    public function overwrite(bool $overwrite = true): self
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
