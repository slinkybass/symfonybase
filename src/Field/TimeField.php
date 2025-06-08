<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField as EasyField;

class TimeField
{
    use FieldTrait;

    public const OPTION_PLUGIN = 'data-time-field';

    public const OPTION_MINUTE_INCREMENT = 'data-date-minute-increment';
    public const OPTION_TIME_PATTERN = 'timePattern';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);
        $instance->field->getAsDto()->setAssets(new AssetsDto());

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-time')->onlyOnForms())
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

    public function setMinuteIncrement(?int $val): self
    {
        $this->setHtmlAttribute(self::OPTION_MINUTE_INCREMENT, $val);

        return $this;
    }

    public function setTimezone(?string $timezoneId): self
    {
        $this->setCustomOption(DateField::OPTION_TIMEZONE, $timezoneId);

        return $this;
    }

    public function setFormat(?string $timeFormatOrPattern): self
    {
        $this->setCustomOption(self::OPTION_TIME_PATTERN, $timeFormatOrPattern);

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
