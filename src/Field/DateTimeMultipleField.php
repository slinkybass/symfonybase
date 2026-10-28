<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\FlatpickrCalendarTrait;
use App\Field\Trait\FlatpickrClockTrait;
use App\Field\Trait\FlatpickrFieldTrait;
use App\Field\Trait\FlatpickrRangeTrait;
use App\Form\Type\DateMultipleType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField as EasyField;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Contracts\Translation\TranslatableInterface;

class DateTimeMultipleField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use FlatpickrFieldTrait;
    use FlatpickrCalendarTrait;
    use FlatpickrClockTrait;
    use FlatpickrRangeTrait;

    private EasyField $innerField;
    public const DEFAULT_ENTRY_TYPE = DateTimeType::class;

    /** Build a multi date-time field (Flatpickr `multiple`/`range`, posted as CSV). */
    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
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
        $this->setFormTypeOption('entry_type', self::DEFAULT_ENTRY_TYPE);
    }

    protected function defaultDateHtmlFormat(): string
    {
        return 'Y-m-d H:i:s';
    }

    /** Plugin: one Flatpickr input. Off: CollectionType of datetime rows. */
    public function enablePlugin(bool $enable = true): self
    {
        $this->enableMappedPlugin('form-type-datetime', DateTimeField::OPTION_PLUGIN, $enable);

        $this->setFormType($enable ? DateMultipleType::class : CollectionType::class);
        $this->setDateMode($enable ? self::DATE_MODE_MULTIPLE : self::DATE_MODE_SINGLE);
        if ($enable) {
            $this->setRangeSeparator();
            $this->setTemplatePath('field/datetimeMultiple.html.twig');
        } else {
            $this->setTemplateName('crud/field/array');
        }

        return $this;
    }

    /** Last selectable instant (`max` attribute, `Y-m-d H:i:s`). */
    public function setMax(\DateTimeInterface|string|null $max): self
    {
        return $this->setHtmlMinMax(self::OPTION_MAX, $max, 'Y-m-d H:i:s');
    }

    /** First selectable instant (`min` attribute, `Y-m-d H:i:s`). */
    public function setMin(\DateTimeInterface|string|null $min): self
    {
        return $this->setHtmlMinMax(self::OPTION_MIN, $min, 'Y-m-d H:i:s');
    }
}
