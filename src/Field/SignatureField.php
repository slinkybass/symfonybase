<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField as EasyField;

class SignatureField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const OPTION_PLUGIN = 'data-signature-field';

    public const OPTION_SIGNATURE_SHOW_INPUT = 'data-signature-show-input';
    public const OPTION_SIGNATURE_SHOW_UNDO = 'data-signature-show-undo';
    public const OPTION_SIGNATURE_SHOW_CLEAR = 'data-signature-show-clear';

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
        $this->enablePlugin();
        $this->setFormTypeOption('block_prefix', 'signature');
        $this->setTemplatePath('field/media.html.twig');
    }

    public function enablePlugin(bool $enable = true): self
    {
        $this->dto->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new('form-type-signature')->onlyOnForms());
        }
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
