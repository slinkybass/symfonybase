<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField as EasyField;

class TimeField
{
    use FieldTrait;

    public const OPTION_PLUGIN = 'data-time-field';

    public const OPTION_DATE_ENABLE_SECONDS = 'data-date-enable-seconds';
    public const OPTION_DATE_MINUTE_INCREMENT = 'data-date-minute-increment';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->plugin()
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $enable = true): self
    {
        $this->field->getAsDto()->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new('form-type-time')->onlyOnForms());
        }

        $this->setHtmlAttribute(self::OPTION_PLUGIN, json_encode($enable));

        return $this;
    }

    public function setMax(\DateTime|string|null $max): self
    {
        $this->setHtmlAttribute(DateField::OPTION_MAX, $max instanceof \DateTime ? $max->format('H:i:s') : $max);

        return $this;
    }

    public function setMin(\DateTime|string|null $min): self
    {
        $this->setHtmlAttribute(DateField::OPTION_MIN, $min instanceof \DateTime ? $min->format('H:i:s') : $min);

        return $this;
    }

    public function isInline(bool $inline = true): self
    {
        $this->setHtmlAttribute(DateField::OPTION_DATE_INLINE, json_encode($inline));

        return $this;
    }

    public function enableSeconds(bool $enable = true): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_ENABLE_SECONDS, json_encode($enable));

        return $this;
    }

    public function setMinuteIncrement(int $minutes): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_MINUTE_INCREMENT, $minutes);

        return $this;
    }

    public function setTimezone(string $timezone): self
    {
        $this->field->setTimezone($timezone);

        return $this;
    }

    public function setFormat(string $timeFormat): self
    {
        $this->field->setFormat($timeFormat);

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
