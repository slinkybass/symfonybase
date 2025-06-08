<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
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
        $instance->field->getAsDto()->setAssets(new AssetsDto());

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-datetime')->onlyOnForms())
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
        $this->setHtmlAttribute(DateField::OPTION_MAX, $val);

        return $this;
    }

    public function setMin(?string $val): self
    {
        $this->setHtmlAttribute(DateField::OPTION_MIN, $val);

        return $this;
    }

    public function inline(bool $val = true): self
    {
        $this->setHtmlAttribute(DateField::OPTION_INLINE, json_encode($val));

        return $this;
    }

    public function single(bool $val = true): self
    {
        $this->setHtmlAttribute(DateField::OPTION_MODE, $val ? DateField::MODE_SINGLE : DateField::MODE_MULTIPLE);
        $this->setFormType($val ? DateTimeType::class : TextType::class);
        if ($val) {
            $this->setTemplateName('crud/field/datetime');
        } else {
            $this->setTemplatePath('field/datetimeMultiple.html.twig');
        }

        return $this;
    }

    public function multiple(bool $val = true): self
    {
        $this->setHtmlAttribute(DateField::OPTION_MODE, $val ? DateField::MODE_MULTIPLE : DateField::MODE_SINGLE);
        $this->setFormType($val ? TextType::class : DateTimeType::class);
        if ($val) {
            $this->setTemplatePath('field/datetimeMultiple.html.twig');
        } else {
            $this->setTemplateName('crud/field/datetime');
        }

        return $this;
    }

    public function range(bool $val = true): self
    {
        $this->setHtmlAttribute(DateField::OPTION_MODE, $val ? DateField::MODE_RANGE : DateField::MODE_SINGLE);
        $this->setFormType($val ? TextType::class : DateTimeType::class);
        if ($val) {
            $this->setTemplatePath('field/datetimeMultiple.html.twig');
        } else {
            $this->setTemplateName('crud/field/datetime');
        }

        return $this;
    }

    public function setDateFormat(?string $val): self
    {
        $this->setCustomOption(DateField::OPTION_DATE_FORMAT, $val);

        return $this;
    }

    public function seAltFormat(?string $val): self
    {
        $this->setCustomOption(DateField::OPTION_ALT_FORMAT, $val);

        return $this;
    }

    public function setEnabledDates(?array $dates): self
    {
        $datesArr = [];
        foreach ($dates as $date) {
            $datesArr[] = $date instanceof \DateTime ? $date->format('Y-m-d') : $date;
        }
        $this->setHtmlAttribute(DateField::OPTION_ENABLED_DATES, implode(',', $datesArr));

        return $this;
    }

    public function setDisabledDates(?array $dates): self
    {
        $datesArr = [];
        foreach ($dates as $date) {
            $datesArr[] = $date instanceof \DateTime ? $date->format('Y-m-d') : $date;
        }
        $this->setHtmlAttribute(DateField::OPTION_DISABLED_DATES, implode(',', $datesArr));

        return $this;
    }

    public function setMinuteIncrement(?int $val): self
    {
        $this->setHtmlAttribute(TimeField::OPTION_MINUTE_INCREMENT, $val);

        return $this;
    }

    public function setTimezone(?string $timezoneId): self
    {
        $this->setCustomOption(DateField::OPTION_TIMEZONE, $timezoneId);

        return $this;
    }

    public function setFormat(?string $dateFormatOrPattern, ?string $timeFormat = self::FORMAT_NONE): self
    {
        $this->setCustomOption(DateField::OPTION_DATE_PATTERN, $dateFormatOrPattern);
        $this->setCustomOption(TimeField::OPTION_TIME_PATTERN, $timeFormat);

        return $this;
    }

    public function renderAsNativeWidget(bool $val = true): self
    {
        if ($val) {
            $this->setCustomOption(DateField::OPTION_WIDGET, DateField::WIDGET_NATIVE);
        } else {
            $this->renderAsChoice();
        }

        return $this;
    }

    public function renderAsChoice(bool $val = true): self
    {
        if ($val) {
            $this->setCustomOption(DateField::OPTION_WIDGET, DateField::WIDGET_CHOICE);
        } else {
            $this->renderAsNativeWidget();
        }

        return $this;
    }

    public function renderAsText(bool $val = true): self
    {
        if ($val) {
            $this->setCustomOption(DateField::OPTION_WIDGET, DateField::WIDGET_TEXT);
        } else {
            $this->renderAsNativeWidget();
        }

        return $this;
    }
}
