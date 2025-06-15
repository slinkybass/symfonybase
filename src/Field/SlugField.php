<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField as EasyField;

class SlugField
{
    use FieldTrait;

    public const OPTION_PLUGIN = 'data-slug-field';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);
        $instance->field->getAsDto()->setAssets(new AssetsDto());

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-slug')->onlyOnForms())
            ->plugin(true)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($val));

        return $this;
    }

    public function setTarget(string|array $fieldName): self
    {
        $this->field->setTargetFieldName($fieldName);

        return $this;
    }

    public function setConfirmText(string $message): self
    {
        $this->field->setUnlockConfirmationMessage($message);

        return $this;
    }
}
