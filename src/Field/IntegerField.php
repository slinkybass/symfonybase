<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField as EasyField;

class IntegerField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

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
    }

    public function pluginSlider(bool $enable = true): self
    {
        $this->dto->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new('form-type-slider')->onlyOnForms());
        }
        $this->setHtmlAttribute(FloatField::OPTION_PLUGIN_SLIDER, json_encode($enable));

        return $this;
    }

    public function sliderShowInput(bool $show = true): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_SLIDER_SHOW_INPUT, json_encode($show));

        return $this;
    }

    public function sliderShowTooltip(bool $tooltips = true): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_SLIDER_TOOLTIPS, json_encode($tooltips));

        return $this;
    }

    public function sliderConnectUpper(): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_SLIDER_CONNECT, FloatField::SLIDER_CONNECT_UPPER);

        return $this;
    }

    public function sliderConnectLower(): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_SLIDER_CONNECT, FloatField::SLIDER_CONNECT_LOWER);

        return $this;
    }

    public function sliderPips(bool $pips = true): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_SLIDER_PIPS, json_encode($pips));

        return $this;
    }

    public function setMax(int|float|null $val): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_MAX, $val);

        return $this;
    }

    public function setMin(int|float|null $val): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_MIN, $val);

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
