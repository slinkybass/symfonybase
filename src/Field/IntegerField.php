<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\NoUiSliderFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `IntegerField` wrapper with optional noUiSlider. */
class IntegerField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use NoUiSliderFieldTrait;

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
    }

    public function setMax(int|float|null $max): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_MAX, $max);

        return $this;
    }

    public function setMin(int|float|null $min): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_MIN, $min);

        return $this;
    }

    public function setNumberFormat(string $numberFormat): self
    {
        $this->innerField->setNumberFormat($numberFormat);

        return $this;
    }

    public function setThousandsSeparator(string $separator): self
    {
        $this->innerField->setThousandsSeparator($separator);

        return $this;
    }
}
