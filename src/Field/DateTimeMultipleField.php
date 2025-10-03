<?php

namespace App\Field;

use App\Form\Type\DateMultipleType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField as EasyField;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class DateTimeMultipleField
{
    use FieldTrait;

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->plugin()
            ->setFormTypeOption('entry_type', DateTimeType::class)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function plugin(bool $enable = true): self
    {
        $this->field->getAsDto()->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new('form-type-datetime')->onlyOnForms());
        }

        $this->setHtmlAttribute(DateTimeField::OPTION_PLUGIN, json_encode($enable));
        $this->setFormType($enable ? DateMultipleType::class : CollectionType::class);
        $this->setHtmlAttribute(DateField::OPTION_DATE_MODE, $enable ? DateField::DATE_MODE_MULTIPLE : DateField::DATE_MODE_SINGLE);

        if ($enable) {
            $this->setTemplatePath('field/datetimeMultiple.html.twig');
        } else {
            $this->setTemplateName('crud/field/array');
        }

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

    public function isRange(bool $range = true): self
    {
        $this->setHtmlAttribute(DateField::OPTION_DATE_MODE, !$range ? DateField::DATE_MODE_MULTIPLE : DateField::DATE_MODE_RANGE);

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

    public function enableSeconds(bool $enable = true): self
    {
        $this->setHtmlAttribute(TimeField::OPTION_DATE_ENABLE_SECONDS, json_encode($enable));

        return $this;
    }

    public function setMinuteIncrement(int $val): self
    {
        $this->setHtmlAttribute(TimeField::OPTION_DATE_MINUTE_INCREMENT, $val);

        return $this;
    }
}
