<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField as EasyField;

class TimeField
{
    use FieldTrait;

    public const OPTION_PLUGIN = 'data-time-field';

    public const OPTION_DATE_MINUTE_INCREMENT = 'data-date-minute-increment';

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
        $this->setHtmlAttribute(DateField::OPTION_DATE_INLINE, json_encode($val));

        return $this;
    }

    public function setMinuteIncrement(?int $val): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_MINUTE_INCREMENT, $val);

        return $this;
    }

    public function setTimezone(string $timezoneId): self
    {
        $this->field->setTimezone($timezoneId);

        return $this;
    }

    public function setFormat(string $timeFormatOrPattern): self
    {
        $this->field->setFormat($timeFormatOrPattern);

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
