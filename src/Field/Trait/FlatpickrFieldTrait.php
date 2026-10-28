<?php

namespace App\Field\Trait;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;

trait FlatpickrFieldTrait
{
    public const OPTION_MAX = 'max';
    public const OPTION_MIN = 'min';

    public const OPTION_DATE_INLINE = 'data-date-inline';
    public const OPTION_DATE_FORMAT = 'data-date-format';
    public const OPTION_DATE_ALT_FORMAT = 'data-date-alt-format';
    public const OPTION_DATE_ALT_INPUT = 'data-date-alt-input';
    public const OPTION_DATE_ALT_INPUT_CLASS = 'data-date-alt-input-class';
    public const OPTION_DATE_ALLOW_INPUT = 'data-date-allow-input';
    public const OPTION_DATE_ALLOW_INVALID_PRELOAD = 'data-date-allow-invalid-preload';
    public const OPTION_DATE_ANIMATE = 'data-date-animate';
    public const OPTION_DATE_CLICK_OPENS = 'data-date-click-opens';
    public const OPTION_DATE_CLOSE_ON_SELECT = 'data-date-close-on-select';
    public const OPTION_DATE_DISABLE_MOBILE = 'data-date-disable-mobile';
    public const OPTION_DATE_LOCALE = 'data-date-locale';
    public const OPTION_DATE_NOW = 'data-date-now';
    public const OPTION_DATE_POSITION = 'data-date-position';
    public const OPTION_DATE_STATIC = 'data-date-static';
    public const OPTION_DATE_WRAP = 'data-date-wrap';

    public const DATE_POSITION_AUTO = 'auto';
    public const DATE_POSITION_ABOVE = 'above';
    public const DATE_POSITION_BELOW = 'below';
    public const DATE_POSITION_AUTO_LEFT = 'auto left';
    public const DATE_POSITION_AUTO_CENTER = 'auto center';
    public const DATE_POSITION_AUTO_RIGHT = 'auto right';
    public const DATE_POSITION_ABOVE_LEFT = 'above left';
    public const DATE_POSITION_ABOVE_CENTER = 'above center';
    public const DATE_POSITION_ABOVE_RIGHT = 'above right';
    public const DATE_POSITION_BELOW_LEFT = 'below left';
    public const DATE_POSITION_BELOW_CENTER = 'below center';
    public const DATE_POSITION_BELOW_RIGHT = 'below right';

    /** Format a date/time for an HTML attribute, or return a string as-is. */
    public static function htmlValue(\DateTimeInterface|string|null $value, string $format): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof \DateTimeInterface ? $value->format($format) : $value;
    }

    /** Join dates as a CSV for `data-date-enabled` / `disabled` / default-date lists. */
    public static function htmlDateList(?array $dates, string $format = 'Y-m-d'): string
    {
        $parts = [];
        foreach ($dates ?? [] as $date) {
            $parts[] = $date instanceof \DateTimeInterface ? $date->format($format) : $date;
        }

        return implode(',', $parts);
    }

    protected function enableMappedPlugin(string $entry, string $flagAttr, bool $enable): self
    {
        $this->dto->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new($entry)->onlyOnForms());
        }
        $this->setHtmlAttribute($flagAttr, json_encode($enable));

        return $this;
    }

    protected function setHtmlMinMax(string $attribute, \DateTimeInterface|string|null $value, string $format): self
    {
        $this->setHtmlAttribute($attribute, self::htmlValue($value, $format));

        return $this;
    }

    protected function setDateBoolAttr(string $option, bool $value): self
    {
        $this->setHtmlAttribute($option, json_encode($value));

        return $this;
    }

    /** Show the calendar/clock inline instead of as a popup. */
    public function isInline(bool $inline = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_INLINE, $inline);
    }

    /** Moment token for the value posted to the server (`data-date-format`). */
    public function setDateFormat(string $dateFormat): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_FORMAT, $dateFormat);

        return $this;
    }

    /** Moment token for the visible alt input (`data-date-alt-format`). */
    public function setDateAltFormat(string $dateAltFormat): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_ALT_FORMAT, $dateAltFormat);

        return $this;
    }

    /** Use a readable alt input while the original holds the posted value. */
    public function isAltInput(bool $enable = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_ALT_INPUT, $enable);
    }

    /** Extra CSS class on Flatpickr's alt input. */
    public function setAltInputClass(?string $class): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_ALT_INPUT_CLASS, $class);

        return $this;
    }

    /** Allow typing in the input (overrides the `readonly` attribute). */
    public function isAllowInput(bool $allow = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_ALLOW_INPUT, $allow);
    }

    /** Keep an invalid preload value instead of clearing the field. */
    public function isAllowInvalidPreload(bool $allow = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_ALLOW_INVALID_PRELOAD, $allow);
    }

    /** Enable Flatpickr open/close animation. */
    public function isAnimate(bool $animate = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_ANIMATE, $animate);
    }

    /** Open the picker when the input is clicked. */
    public function isClickOpens(bool $enable = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_CLICK_OPENS, $enable);
    }

    /** Close the picker after a date is selected. */
    public function isCloseOnSelect(bool $enable = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_CLOSE_ON_SELECT, $enable);
    }

    /** Always use Flatpickr instead of the native mobile date widget. */
    public function isDisableMobile(bool $disable = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_DISABLE_MOBILE, $disable);
    }

    /** Flatpickr l10n key for this instance (not EasyAdmin `setTimezone()`). */
    public function setPickerLocale(string $locale): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_LOCALE, $locale);

        return $this;
    }

    /** Date/time treated as "today" / "now" in the picker. */
    public function setNow(\DateTimeInterface|string|null $now): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_NOW, self::htmlValue($now, 'Y-m-d H:i:s'));

        return $this;
    }

    /**
     * Picker position relative to the input.
     *
     * @param self::DATE_POSITION_* $position
     */
    public function setPosition(string $position): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_POSITION, $position);

        return $this;
    }

    /** Position the calendar inside the wrapper next to the input. */
    public function isStatic(bool $enable = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_STATIC, $enable);
    }

    /** Use Flatpickr wrap mode for custom input groups. */
    public function isWrap(bool $enable = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_WRAP, $enable);
    }
}
