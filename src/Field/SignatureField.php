<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\SignatureFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `TextField` wrapper with SignaturePad plugin options. */
class SignatureField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use SignatureFieldTrait;

    private EasyField $innerField;

    public const DEFAULT_BLOCK_PREFIX = 'signature';
    public const DEFAULT_TEMPLATE_PATH = 'field/media.html.twig';

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
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
        $this->setFormTypeOption('block_prefix', self::DEFAULT_BLOCK_PREFIX);
        $this->setTemplatePath(self::DEFAULT_TEMPLATE_PATH);
    }

    /** Load SignaturePad (`data-signature-field`). `false` keeps a plain text input. */
    public function enablePlugin(bool $enable = true): self
    {
        return $this->enableMappedPlugin('form-type-signature', self::OPTION_PLUGIN, $enable);
    }
}
