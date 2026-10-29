<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\IMaskFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `TextField` wrapper with IMask plugin options. */
class MaskField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use IMaskFieldTrait;

    private EasyField $innerField;

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
    }

    /** Load IMask (`data-mask-field`). `false` keeps a plain text input. */
    public function enablePlugin(bool $enable = true): self
    {
        return $this->enableMappedPlugin('form-type-mask', self::OPTION_PLUGIN, $enable);
    }
}
