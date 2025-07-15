<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField as EasyField;

class FloatField
{
    use FieldTrait;

    public const OPTION_HTML5 = 'html5';
    public const OPTION_STEP = 'step';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->setDecimals(2)
            ->setStep(0.1)
            ->setFormTypeOption(self::OPTION_HTML5, true)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function setStep(float $step): self
    {
        $this->field->setHtmlAttribute(self::OPTION_STEP, $step);

        return $this;
    }

    public function setDecimals(int $decimals): self
    {
        $this->field->setNumDecimals($decimals);

        return $this;
    }

    public function setRoundingMode(int $mode): self
    {
        $this->field->setRoundingMode($mode);

        return $this;
    }

    public function storedAsString(bool $asString = true): self
    {
        $this->field->setStoredAsString($asString);

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

    public function setDecimalSeparator(string $separator): self
    {
        $this->field->setDecimalSeparator($separator);

        return $this;
    }
}
