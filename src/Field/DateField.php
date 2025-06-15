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
            ->plugin()
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $enable = true): self
    {
        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($enable));

        return $this;
    }

    public function setMax(\DateTime|string|null $max): self
    {
        $this->setHtmlAttribute(self::OPTION_MAX, $max instanceof \DateTime ? $max->format('Y-m-d') : $max);

        return $this;
    }

    public function setMin(\DateTime|string|null $min): self
    {
        $this->setHtmlAttribute(self::OPTION_MIN, $min instanceof \DateTime ? $min->format('Y-m-d') : $min);

        return $this;
    }

    public function inline(bool $inline = true): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_INLINE, json_encode($inline));

        return $this;
    }

    public function single(bool $single = true): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_MODE, $single ? self::DATE_MODE_SINGLE : self::DATE_MODE_MULTIPLE);
        $this->setFormType($single ? DateType::class : TextType::class);
        if ($single) {
            $this->setTemplateName('crud/field/date');
        } else {
            $this->setTemplatePath('field/dateMultiple.html.twig');
        }

        return $this;
    }

    public function multiple(bool $multiple = true): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_MODE, $multiple ? self::DATE_MODE_MULTIPLE : self::DATE_MODE_SINGLE);
        $this->setFormType($multiple ? TextType::class : DateType::class);
        if ($multiple) {
            $this->setTemplatePath('field/dateMultiple.html.twig');
        } else {
            $this->setTemplateName('crud/field/date');
        }

        return $this;
    }

    public function range(bool $range = true): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_MODE, $range ? self::DATE_MODE_RANGE : self::DATE_MODE_SINGLE);
        $this->setFormType($range ? TextType::class : DateType::class);
        if ($range) {
            $this->setTemplatePath('field/dateMultiple.html.twig');
        } else {
            $this->setTemplateName('crud/field/date');
        }

        return $this;
    }

    public function setDateFormat(string $dateFormat): self
    {
        $this->setCustomOption(self::OPTION_DATE_FORMAT, $dateFormat);

        return $this;
    }

    public function setDateAltFormat(string $dateAltFormat): self
    {
        $this->setCustomOption(self::OPTION_DATE_ALT_FORMAT, $dateAltFormat);

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

    public function setTimezone(string $timezone): self
    {
        $this->field->setTimezone($timezone);

        return $this;
    }

    public function setFormat(string $dateFormat): self
    {
        $this->field->setFormat($dateFormat);

        return $this;
    }

    public function renderAsChoice(bool $choice = true): self
    {
        if ($choice) {
            $this->field->renderAsChoice();
        } else {
            $this->field->renderAsNativeWidget();
        }

        return $this;
    }
}
