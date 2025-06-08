<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField as EasyField;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DateField
{
    use FieldTrait;

    public const OPTION_PLUGIN = 'data-date-field';

    public const OPTION_MAX = 'max';
    public const OPTION_MIN = 'min';
    public const OPTION_INLINE = 'data-date-inline';
    public const OPTION_MODE = 'data-date-mode';
    public const MODE_SINGLE = 'single';
    public const MODE_MULTIPLE = 'multiple';
    public const MODE_RANGE = 'range';
    public const OPTION_DATE_FORMAT = 'data-date-format';
    public const OPTION_ALT_FORMAT = 'data-date-alt-format';
    public const OPTION_ENABLED_DATES = 'data-date-enabled';
    public const OPTION_DISABLED_DATES = 'data-date-disabled';

    public const OPTION_TIMEZONE = 'timezone';
    public const OPTION_DATE_PATTERN = 'datePattern';

    public const OPTION_WIDGET = 'widget';
    public const WIDGET_NATIVE = 'native';
    public const WIDGET_CHOICE = 'choice';
    public const WIDGET_TEXT = 'text';

    public const FORMAT_FULL = 'full';
    public const FORMAT_LONG = 'long';
    public const FORMAT_MEDIUM = 'medium';
    public const FORMAT_SHORT = 'short';
    public const FORMAT_NONE = 'none';
    public const VALID_DATE_FORMATS = [self::FORMAT_NONE, self::FORMAT_SHORT, self::FORMAT_MEDIUM, self::FORMAT_LONG, self::FORMAT_FULL];

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);
        $instance->field->getAsDto()->setAssets(new AssetsDto());

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
        $this->setHtmlAttribute(self::OPTION_INLINE, json_encode($val));

        return $this;
    }

    public function single(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_MODE, $val ? self::MODE_SINGLE : self::MODE_MULTIPLE);
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
        $this->setHtmlAttribute(self::OPTION_MODE, $val ? self::MODE_MULTIPLE : self::MODE_SINGLE);
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
        $this->setHtmlAttribute(self::OPTION_MODE, $val ? self::MODE_RANGE : self::MODE_SINGLE);
        $this->setFormType($val ? TextType::class : DateType::class);
        if ($val) {
            $this->setTemplatePath('field/dateMultiple.html.twig');
        } else {
            $this->setTemplateName('crud/field/date');
        }

        return $this;
    }

    public function setDateFormat(?string $val): self
    {
        $this->setCustomOption(self::OPTION_DATE_FORMAT, $val);

        return $this;
    }

    public function seAltFormat(?string $val): self
    {
        $this->setCustomOption(self::OPTION_ALT_FORMAT, $val);

        return $this;
    }

    public function setEnabledDates(?array $dates): self
    {
        $datesArr = [];
        foreach ($dates as $date) {
            $datesArr[] = $date instanceof \DateTime ? $date->format('Y-m-d') : $date;
        }
        $this->setHtmlAttribute(self::OPTION_ENABLED_DATES, implode(',', $datesArr));

        return $this;
    }

    public function setDisabledDates(?array $dates): self
    {
        $datesArr = [];
        foreach ($dates as $date) {
            $datesArr[] = $date instanceof \DateTime ? $date->format('Y-m-d') : $date;
        }
        $this->setHtmlAttribute(self::OPTION_DISABLED_DATES, implode(',', $datesArr));

        return $this;
    }

    public function setTimezone(?string $timezoneId): self
    {
        $this->setCustomOption(self::OPTION_TIMEZONE, $timezoneId);

        return $this;
    }

    public function setFormat(?string $dateFormatOrPattern): self
    {
        $this->setCustomOption(self::OPTION_DATE_PATTERN, $dateFormatOrPattern);

        return $this;
    }

    public function renderAsNativeWidget(bool $val = true): self
    {
        if ($val) {
            $this->setCustomOption(self::OPTION_WIDGET, self::WIDGET_NATIVE);
        } else {
            $this->renderAsChoice();
        }

        return $this;
    }

    public function renderAsChoice(bool $val = true): self
    {
        if ($val) {
            $this->setCustomOption(self::OPTION_WIDGET, self::WIDGET_CHOICE);
        } else {
            $this->renderAsNativeWidget();
        }

        return $this;
    }

    public function renderAsText(bool $val = true): self
    {
        if ($val) {
            $this->setCustomOption(self::OPTION_WIDGET, self::WIDGET_TEXT);
        } else {
            $this->renderAsNativeWidget();
        }

        return $this;
    }
}
