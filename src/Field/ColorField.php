<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\SpectrumFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `ColorField` wrapper with Spectrum plugin options. */
class ColorField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use SpectrumFieldTrait;

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

    /** Load Spectrum (`data-color-field`). `false` keeps a native color widget. */
    public function enablePlugin(bool $enable = true): self
    {
        return $this->enableMappedPlugin('form-type-color', self::OPTION_PLUGIN, $enable);
    }

    /** EasyAdmin index/detail color swatch. */
    public function showSample(bool $show = true): self
    {
        $this->innerField->showSample($show);

        return $this;
    }

    /** EasyAdmin index/detail formatted color value. */
    public function showValue(bool $show = true): self
    {
        $this->innerField->showValue($show);

        return $this;
    }
}
