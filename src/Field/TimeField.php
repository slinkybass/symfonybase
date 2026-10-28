<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\FlatpickrClockTrait;
use App\Field\Trait\FlatpickrFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

class TimeField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use FlatpickrFieldTrait;
    use FlatpickrClockTrait;

    private EasyField $innerField;

    public const OPTION_PLUGIN = 'data-time-field';

    /** EasyAdmin display tokens for `setFormat()`, not `data-date-format`. */
    public const DISPLAY_FORMAT_FULL = 'full';
    public const DISPLAY_FORMAT_LONG = 'long';
    public const DISPLAY_FORMAT_MEDIUM = 'medium';
    public const DISPLAY_FORMAT_SHORT = 'short';
    public const DISPLAY_FORMAT_NONE = 'none';

    /** Build a time-of-day field (Flatpickr time plugin, no calendar). */
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
    }

    /** Load Flatpickr (`data-time-field`). `false` keeps a native/choice widget. */
    public function enablePlugin(bool $enable = true): self
    {
        return $this->enableMappedPlugin('form-type-time', self::OPTION_PLUGIN, $enable);
    }

    /** Latest selectable time. Writes `data-date-max-time`, not HTML `max`. */
    public function setMax(\DateTimeInterface|string|null $max): self
    {
        return $this->setMaxTime($max);
    }

    /** Earliest selectable time. Writes `data-date-min-time`, not HTML `min`. */
    public function setMin(\DateTimeInterface|string|null $min): self
    {
        return $this->setMinTime($min);
    }

    /** EasyAdmin index/detail timezone. Does not configure Flatpickr. */
    public function setTimezone(string $timezone): self
    {
        $this->innerField->setTimezone($timezone);

        return $this;
    }

    /**
     * EasyAdmin index/detail time display format, not the picker. Also accepts an ICU pattern.
     *
     * @param self::DISPLAY_FORMAT_*|string $timeFormat
     */
    public function setFormat(string $timeFormat): self
    {
        $this->innerField->setFormat($timeFormat);

        return $this;
    }

    /** Native select widgets instead of Flatpickr. `false` restores the plugin. */
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
