<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\FlatpickrCalendarTrait;
use App\Field\Trait\FlatpickrClockTrait;
use App\Field\Trait\FlatpickrFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

class DateTimeField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use FlatpickrFieldTrait;
    use FlatpickrCalendarTrait;
    use FlatpickrClockTrait;

    private EasyField $innerField;

    public const OPTION_PLUGIN = 'data-datetime-field';

    /** EasyAdmin display tokens for `setFormat()`, not `data-date-format`. */
    public const DISPLAY_FORMAT_FULL = 'full';
    public const DISPLAY_FORMAT_LONG = 'long';
    public const DISPLAY_FORMAT_MEDIUM = 'medium';
    public const DISPLAY_FORMAT_SHORT = 'short';
    public const DISPLAY_FORMAT_NONE = 'none';

    /** Build a date-and-time field (Flatpickr datetime plugin). */
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

    protected function defaultDateHtmlFormat(): string
    {
        return 'Y-m-d H:i:s';
    }

    /** Load Flatpickr (`data-datetime-field`). `false` keeps a native/choice widget. */
    public function enablePlugin(bool $enable = true): self
    {
        return $this->enableMappedPlugin('form-type-datetime', self::OPTION_PLUGIN, $enable);
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

    /** EasyAdmin index/detail timezone. Does not configure Flatpickr. */
    public function setTimezone(string $timezone): self
    {
        $this->innerField->setTimezone($timezone);

        return $this;
    }

    /**
     * EasyAdmin index/detail date+time display format, not the picker. Also accepts ICU patterns.
     *
     * @param self::DISPLAY_FORMAT_*|string $dateFormat
     * @param self::DISPLAY_FORMAT_*|string $timeFormat
     */
    public function setFormat(string $dateFormat, string $timeFormat): self
    {
        $this->innerField->setFormat($dateFormat, $timeFormat);

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
