<?php

namespace App\Field\Trait;

/**
 * Used together with {@see FlatpickrFieldTrait}.
 *
 * @mixin FlatpickrFieldTrait
 *
 * @method static string|null htmlValue(\DateTimeInterface|string|null $value, string $format)
 */
trait FlatpickrClockTrait
{
    public const OPTION_DATE_AUTO_FILL_DEFAULT_TIME = 'data-date-auto-fill-default-time';
    public const OPTION_DATE_DEFAULT_HOUR = 'data-date-default-hour';
    public const OPTION_DATE_DEFAULT_MINUTE = 'data-date-default-minute';
    public const OPTION_DATE_DEFAULT_SECONDS = 'data-date-default-seconds';
    public const OPTION_DATE_ENABLE_SECONDS = 'data-date-enable-seconds';
    public const OPTION_DATE_HOUR_INCREMENT = 'data-date-hour-increment';
    public const OPTION_DATE_MAX_TIME = 'data-date-max-time';
    public const OPTION_DATE_MIN_TIME = 'data-date-min-time';
    public const OPTION_DATE_MINUTE_INCREMENT = 'data-date-minute-increment';
    public const OPTION_DATE_TIME_24HR = 'data-date-time-24hr';

    /** Fill hour/minute with defaults when a date is picked with no time. */
    public function isAutoFillDefaultTime(bool $enable = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_AUTO_FILL_DEFAULT_TIME, $enable);
    }

    /** Initial hour in the time spinner (0–23). */
    public function setDefaultHour(int $hour): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_DEFAULT_HOUR, $hour);

        return $this;
    }

    /** Initial minute in the time spinner. */
    public function setDefaultMinute(int $minute): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_DEFAULT_MINUTE, $minute);

        return $this;
    }

    /** Initial seconds in the time spinner. */
    public function setDefaultSeconds(int $seconds): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_DEFAULT_SECONDS, $seconds);

        return $this;
    }

    /** Show and post seconds (`HH:mm:ss`). */
    public function isEnableSeconds(bool $enable = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_ENABLE_SECONDS, $enable);
    }

    /** Step for the hour spinner (scroll/arrows). */
    public function setHourIncrement(int $hours): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_HOUR_INCREMENT, $hours);

        return $this;
    }

    /** Earliest selectable time of day (`H:i:s`). Independent of `setMin()` on datetime. */
    public function setMinTime(\DateTimeInterface|string|null $minTime): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_MIN_TIME, self::htmlValue($minTime, 'H:i:s'));

        return $this;
    }

    /** Latest selectable time of day (`H:i:s`). Independent of `setMax()` on datetime. */
    public function setMaxTime(\DateTimeInterface|string|null $maxTime): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_MAX_TIME, self::htmlValue($maxTime, 'H:i:s'));

        return $this;
    }

    /** Step for the minute spinner (scroll/arrows). */
    public function setMinuteIncrement(int $minutes): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_MINUTE_INCREMENT, $minutes);

        return $this;
    }

    /** 24-hour clock without AM/PM. */
    public function isTime24hr(bool $enable = true): self
    {
        return $this->setDateBoolAttr(self::OPTION_DATE_TIME_24HR, $enable);
    }
}
