<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField as EasyField;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DateTimeField
{
    use FieldTrait;

    public const OPTION_PLUGIN = 'data-datetime-field';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-datetime')->onlyOnForms())
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
        $this->setHtmlAttribute(DateField::OPTION_MAX, $max instanceof \DateTime ? $max->format('Y-m-d H:i:s') : $max);

        return $this;
    }

    public function setMin(\DateTime|string|null $min): self
    {
        $this->setHtmlAttribute(DateField::OPTION_MIN, $min instanceof \DateTime ? $min->format('Y-m-d H:i:s') : $min);

        return $this;
    }

    public function isInline(bool $inline = true): self
    {
        $this->setHtmlAttribute(DateField::OPTION_DATE_INLINE, json_encode($inline));

        return $this;
    }

    public function isSingle(bool $single = true): self
    {
        $this->setHtmlAttribute(DateField::OPTION_DATE_MODE, $single ? DateField::DATE_MODE_SINGLE : DateField::DATE_MODE_MULTIPLE);
        $this->setFormType($single ? DateTimeType::class : TextType::class);
        $this->setTemplateName($single ? 'crud/field/datetime' : 'field/datetimeMultiple.html.twig');

        return $this;
    }

    public function isMultiple(bool $multiple = true): self
    {
        $this->setHtmlAttribute(DateField::OPTION_DATE_MODE, !$multiple ? DateField::DATE_MODE_SINGLE : DateField::DATE_MODE_MULTIPLE);
        $this->setFormType(!$multiple ? DateTimeType::class : TextType::class);
        $this->setTemplateName(!$multiple ? 'crud/field/datetime' : 'field/datetimeMultiple.html.twig');

        return $this;
    }

    public function isRange(bool $range = true): self
    {
        $this->setHtmlAttribute(DateField::OPTION_DATE_MODE, !$range ? DateField::DATE_MODE_SINGLE : DateField::DATE_MODE_RANGE);
        $this->setFormType(!$range ? DateTimeType::class : TextType::class);
        $this->setTemplateName(!$range ? 'crud/field/datetime' : 'field/datetimeMultiple.html.twig');

        return $this;
    }

    public function setDateFormat(string $dateFormat): self
    {
        $this->setCustomOption(DateField::OPTION_DATE_FORMAT, $dateFormat);

        return $this;
    }

    public function setDateAltFormat(string $dateAltFormat): self
    {
        $this->setCustomOption(DateField::OPTION_DATE_ALT_FORMAT, $dateAltFormat);

        return $this;
    }

    public function setEnabledDates(?array $dates): self
    {
        $datesArr = [];
        foreach ($dates as $date) {
            $datesArr[] = $date instanceof \DateTime ? $date->format('Y-m-d') : $date;
        }
        $this->setHtmlAttribute(DateField::OPTION_DATE_ENABLED, implode(',', $datesArr));

        return $this;
    }

    public function setDisabledDates(?array $dates): self
    {
        $datesArr = [];
        foreach ($dates as $date) {
            $datesArr[] = $date instanceof \DateTime ? $date->format('Y-m-d') : $date;
        }
        $this->setHtmlAttribute(DateField::OPTION_DATE_DISABLED, implode(',', $datesArr));

        return $this;
    }

    public function setMinuteIncrement(int $val): self
    {
        $this->setHtmlAttribute(TimeField::OPTION_DATE_MINUTE_INCREMENT, $val);

        return $this;
    }

    public function setTimezone(string $timezone): self
    {
        $this->field->setTimezone($timezone);

        return $this;
    }

    public function setFormat(string $dateFormat, string $timeFormat): self
    {
        $this->field->setFormat($dateFormat, $timeFormat);

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
