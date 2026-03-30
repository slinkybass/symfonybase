<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField as EasyField;

class DateField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const OPTION_MAX = 'max';
    public const OPTION_MIN = 'min';

    public const OPTION_PLUGIN = 'data-date-field';

    public const OPTION_DATE_INLINE = 'data-date-inline';
    public const OPTION_DATE_MODE = 'data-date-mode';
    public const OPTION_DATE_FORMAT = 'data-date-format';
    public const OPTION_DATE_ALT_FORMAT = 'data-date-alt-format';
    public const OPTION_DATE_ENABLED = 'data-date-enabled';
    public const OPTION_DATE_DISABLED = 'data-date-disabled';

    /** modes */
    public const DATE_MODE_SINGLE = 'single';
    public const DATE_MODE_MULTIPLE = 'multiple';
    public const DATE_MODE_RANGE = 'range';

    /** formats */
    public const DATE_FORMAT_FULL = 'full';
    public const DATE_FORMAT_LONG = 'long';
    public const DATE_FORMAT_MEDIUM = 'medium';
    public const DATE_FORMAT_SHORT = 'short';
    public const DATE_FORMAT_NONE = 'none';

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
        $this->enablePlugin();
    }

    public function enablePlugin(bool $enable = true): self
    {
        $this->dto->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new('form-type-date')->onlyOnForms());
        }
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

    public function isInline(bool $inline = true): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_INLINE, json_encode($inline));

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
        $this->innerField->setTimezone($timezone);

        return $this;
    }

    public function setFormat(string $dateFormat): self
    {
        $this->innerField->setFormat($dateFormat);

        return $this;
    }

    public function renderAsChoice(bool $choice = true): self
    {
        if ($choice) {
            $this->innerField->renderAsChoice();
            $this->enablePlugin(false);
        } else {
            $this->innerField->renderAsNativeWidget();
            $this->enablePlugin();
        }

        return $this;
    }
}
