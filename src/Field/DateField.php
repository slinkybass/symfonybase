<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField as EasyField;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DateField
{
    use FieldTrait;

    public const OPTION_MAX = 'max';
    public const OPTION_MIN = 'min';

    public const OPTION_PLUGIN = 'data-date-field';

    public const OPTION_DATE_INLINE = 'data-date-inline';
    public const OPTION_DATE_MODE = 'data-date-mode';
    public const OPTION_DATE_FORMAT = 'data-date-format';
    public const OPTION_DATE_ALT_FORMAT = 'data-date-alt-format';
    public const OPTION_DATE_ENABLED = 'data-date-enabled';
    public const OPTION_DATE_DISABLED = 'data-date-disabled';

    public const DATE_MODE_SINGLE = 'single';
    public const DATE_MODE_MULTIPLE = 'multiple';
    public const DATE_MODE_RANGE = 'range';

    public const FORMAT_FULL = 'full';
    public const FORMAT_LONG = 'long';
    public const FORMAT_MEDIUM = 'medium';
    public const FORMAT_SHORT = 'short';
    public const FORMAT_NONE = 'none';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-date')->onlyOnForms())
            ->plugin(true)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($val));

        return $this;
    }

    public function setMax(?string $val): self
    {
        $this->setHtmlAttribute(self::OPTION_MAX, $val);

        return $this;
    }

    public function setMin(?string $val): self
    {
        $this->setHtmlAttribute(self::OPTION_MIN, $val);

        return $this;
    }

    public function inline(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_INLINE, json_encode($val));

        return $this;
    }

    public function single(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_MODE, $val ? self::DATE_MODE_SINGLE : self::DATE_MODE_MULTIPLE);
        $this->setFormType($val ? DateType::class : TextType::class);
        if ($val) {
            $this->setTemplateName('crud/field/date');
        } else {
            $this->setTemplatePath('field/dateMultiple.html.twig');
        }

        return $this;
    }

    public function multiple(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_MODE, $val ? self::DATE_MODE_MULTIPLE : self::DATE_MODE_SINGLE);
        $this->setFormType($val ? TextType::class : DateType::class);
        if ($val) {
            $this->setTemplatePath('field/dateMultiple.html.twig');
        } else {
            $this->setTemplateName('crud/field/date');
        }

        return $this;
    }

    public function range(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_MODE, $val ? self::DATE_MODE_RANGE : self::DATE_MODE_SINGLE);
        $this->setFormType($val ? TextType::class : DateType::class);
        if ($val) {
            $this->setTemplatePath('field/dateMultiple.html.twig');
        } else {
            $this->setTemplateName('crud/field/date');
        }

        return $this;
    }

    public function setDateFormat(string $val): self
    {
        $this->setCustomOption(self::OPTION_DATE_FORMAT, $val);

        return $this;
    }

    public function seAltFormat(string $val): self
    {
        $this->setCustomOption(self::OPTION_DATE_ALT_FORMAT, $val);

        return $this;
    }

    public function setEnabledDates(?array $dates): self
    {
        $datesArr = [];
        foreach ($dates as $date) {
            $datesArr[] = $date instanceof \DateTime ? $date->format('Y-m-d') : $date;
        }
        $this->setHtmlAttribute(self::OPTION_DATE_ENABLED, implode(',', $datesArr));

        return $this;
    }

    public function setDisabledDates(?array $dates): self
    {
        $datesArr = [];
        foreach ($dates as $date) {
            $datesArr[] = $date instanceof \DateTime ? $date->format('Y-m-d') : $date;
        }
        $this->setHtmlAttribute(self::OPTION_DATE_DISABLED, implode(',', $datesArr));

        return $this;
    }

    public function setTimezone(string $timezoneId): self
    {
        $this->field->setTimezone($timezoneId);

        return $this;
    }

    public function setFormat(string $dateFormatOrPattern): self
    {
        $this->field->setFormat($dateFormatOrPattern);

        return $this;
    }

    public function renderAsChoice(bool $val = true): self
    {
        if ($val) {
            $this->field->renderAsChoice();
        } else {
            $this->field->renderAsNativeWidget();
        }

        return $this;
    }
}
