<?php

namespace App\Field\Trait;

/**
 * Used together with {@see FlatpickrFieldTrait}.
 *
 * @mixin FlatpickrFieldTrait
 *
 * @method static string htmlDateList(?array $dates, string $format = 'Y-m-d')
 * @method static string|null htmlValue(\DateTimeInterface|string|null $value, string $format)
 */
trait FlatpickrCalendarTrait
{
    public const OPTION_DATE_ARIA_DATE_FORMAT = 'data-date-aria-date-format';
    public const OPTION_DATE_DEFAULT_DATE = 'data-date-default-date';
    public const OPTION_DATE_ENABLED = 'data-date-enabled';
    public const OPTION_DATE_DISABLED = 'data-date-disabled';
    public const OPTION_DATE_MONTH_SELECTOR_TYPE = 'data-date-month-selector-type';
    public const OPTION_DATE_NEXT_ARROW = 'data-date-next-arrow';
    public const OPTION_DATE_PREV_ARROW = 'data-date-prev-arrow';
    public const OPTION_DATE_SHORTHAND_CURRENT_MONTH = 'data-date-shorthand-current-month';
    public const OPTION_DATE_SHOW_MONTHS = 'data-date-show-months';
    public const OPTION_DATE_WEEK_NUMBERS = 'data-date-week-numbers';

    public const MONTH_SELECTOR_DROPDOWN = 'dropdown';
    public const MONTH_SELECTOR_STATIC = 'static';

    /** Format of each day in the calendar `aria-label` (screen readers). */
    public function setAriaDateFormat(string $format): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_ARIA_DATE_FORMAT, $format);

        return $this;
    }

    protected function defaultDateHtmlFormat(): string
    {
        return 'Y-m-d';
    }

    /** Initial selected date(s). Arrays become a CSV for multiple/range. */
    public function setDefaultDate(\DateTimeInterface|string|array|null $date): self
    {
        if (is_array($date)) {
            $this->setHtmlAttribute(self::OPTION_DATE_DEFAULT_DATE, self::htmlDateList($date, $this->defaultDateHtmlFormat()));

            return $this;
        }
        $this->setHtmlAttribute(self::OPTION_DATE_DEFAULT_DATE, self::htmlValue($date, $this->defaultDateHtmlFormat()));

        return $this;
    }

    /** Whitelist of selectable calendar days (`Y-m-d` CSV). Wins over disabled dates. */
    public function setEnabledDates(?array $dates): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_ENABLED, self::htmlDateList($dates));

        return $this;
    }

    /** Blacklist of calendar days (`Y-m-d` CSV). Ignored if enabled dates are set. */
    public function setDisabledDates(?array $dates): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_DISABLED, self::htmlDateList($dates));

        return $this;
    }

    /**
     * Month header control.
     *
     * @param self::MONTH_SELECTOR_* $type
     */
    public function setMonthSelectorType(string $type): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_MONTH_SELECTOR_TYPE, $type);

        return $this;
    }

    /** HTML for the next-month arrow. */
    public function setNextArrow(?string $html): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_NEXT_ARROW, $html);

        return $this;
    }

    /** HTML for the previous-month arrow. */
    public function setPrevArrow(?string $html): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_PREV_ARROW, $html);

        return $this;
    }

    /** Show the current month as a short name (e.g. Sep). */
    public function isShorthandCurrentMonth(bool $enable = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_SHORTHAND_CURRENT_MONTH, $enable);
    }

    /** How many months to show at once. */
    public function setShowMonths(int $months): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_SHOW_MONTHS, $months);

        return $this;
    }

    /** Show ISO week numbers in the calendar. */
    public function isWeekNumbers(bool $enable = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_WEEK_NUMBERS, $enable);
    }
}
