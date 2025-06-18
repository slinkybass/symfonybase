<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField as EasyField;

class SignatureField
{
    use FieldTrait;

    public const OPTION_PLUGIN = 'data-signature-field';

    public const OPTION_SIGNATURE_SHOW_INPUT = 'data-signature-show-input';
    public const OPTION_SIGNATURE_SHOW_UNDO = 'data-signature-show-undo';
    public const OPTION_SIGNATURE_SHOW_CLEAR = 'data-signature-show-clear';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-signature')->onlyOnForms())
            ->setFormTypeOption('block_prefix', 'signature')
            ->plugin()
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $enable = true): self
    {
        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($enable));

        return $this;
    }

    public function showInput(bool $show = true): self
    {
        $this->setHtmlAttribute(self::OPTION_SIGNATURE_SHOW_INPUT, json_encode($show));

        return $this;
    }

    public function showUndo(bool $show = true): self
    {
        $this->setHtmlAttribute(self::OPTION_SIGNATURE_SHOW_UNDO, json_encode($show));

        return $this;
    }

    public function showClear(bool $show = true): self
    {
        $this->setHtmlAttribute(self::OPTION_SIGNATURE_SHOW_CLEAR, json_encode($show));

        return $this;
    }
}
