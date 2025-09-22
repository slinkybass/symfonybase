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

        $instance
            ->plugin()
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $enable = true): self
    {
        $this->field->getAsDto()->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new('form-type-slug')->onlyOnForms());
        }

        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($enable));

        return $this;
    }

    public function setTarget(string|array $target): self
    {
        $this->field->setTargetFieldName($target);

        return $this;
    }

    public function setConfirmText(string $message): self
    {
        $this->field->setUnlockConfirmationMessage($message);

        return $this;
    }
}
