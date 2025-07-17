<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField as EasyField;

class IntegerField
{
    use FieldTrait;

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->setDefaultColumns(12);

        return $instance;
    }

    public function pluginSlider(bool $enable = true): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_PLUGIN_SLIDER, json_encode($enable));
        if ($enable) {
            $this->field->addAssetMapperEntries(Asset::new('form-type-slider')->onlyOnForms());
        } else {
            $this->field->getAsDto()->setAssets(new AssetsDto());
        }

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

    public function showInputSlider(bool $show = true): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_SLIDER_SHOW_INPUT, json_encode($show));

        return $this;
    }

    public function showTooltipSlider(bool $tooltips = true): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_SLIDER_TOOLTIPS, json_encode($tooltips));

        return $this;
    }

    public function connectSlider(string $type): self
    {
        $this->setHtmlAttribute(FloatField::OPTION_SLIDER_CONNECT, $type);

        return $this;
    }

    public function setNumberFormat(string $numberFormat): self
    {
        $this->field->setNumberFormat($numberFormat);

        return $this;
    }

    public function setThousandsSeparator(string $separator): self
    {
        $this->field->setThousandsSeparator($separator);

        return $this;
    }
}
