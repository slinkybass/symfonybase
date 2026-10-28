<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\FlatpickrCalendarTrait;
use App\Field\Trait\FlatpickrFieldTrait;
use App\Field\Trait\FlatpickrRangeTrait;
use App\Form\Type\DateMultipleType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField as EasyField;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Contracts\Translation\TranslatableInterface;

class DateMultipleField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use FlatpickrFieldTrait;
    use FlatpickrCalendarTrait;
    use FlatpickrRangeTrait;

    private EasyField $innerField;
    public const DEFAULT_ENTRY_TYPE = DateType::class;

    /** Build a multi-date field (Flatpickr `multiple`/`range`, posted as CSV). */
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

    /** Plugin: one Flatpickr input. Off: CollectionType of date rows. */
    public function enablePlugin(bool $enable = true): self
    {
        $this->enableMappedPlugin('form-type-date', DateField::OPTION_PLUGIN, $enable);

        $this->setFormType($enable ? DateMultipleType::class : CollectionType::class);
        $this->setDateMode($enable ? self::DATE_MODE_MULTIPLE : self::DATE_MODE_SINGLE);
        if ($enable) {
            $this->setRangeSeparator();
            $this->setTemplatePath('field/dateMultiple.html.twig');
        } else {
            $this->setTemplateName('crud/field/array');
        }

        return $this;
    }

    /** Last selectable calendar day (`max` attribute, `Y-m-d`). */
    public function setMax(\DateTimeInterface|string|null $max): self
    {
        return $this->setHtmlMinMax(self::OPTION_MAX, $max, 'Y-m-d');
    }

    /** First selectable calendar day (`min` attribute, `Y-m-d`). */
    public function setMin(\DateTimeInterface|string|null $min): self
    {
        return $this->setHtmlMinMax(self::OPTION_MIN, $min, 'Y-m-d');
    }
}
