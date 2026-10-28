<?php

namespace App\Field\Trait;

use App\Form\Type\DateMultipleType;

/**
 * @mixin FlatpickrFieldTrait
 */
trait FlatpickrRangeTrait
{
    public const OPTION_DATE_MODE = 'data-date-mode';
    public const OPTION_DATE_CONJUNCTION = 'data-date-conjunction';
    public const OPTION_DATE_RANGE_SEPARATOR = 'data-date-range-separator';

    public const DATE_MODE_SINGLE = 'single';
    public const DATE_MODE_MULTIPLE = 'multiple';
    public const DATE_MODE_RANGE = 'range';

    public const DATE_RANGE_SEPARATOR_DEFAULT = DateMultipleType::SEPARATOR;

    /**
     * Flatpickr mode.
     *
     * @param self::DATE_MODE_* $mode
     */
    public function setDateMode(string $mode): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_MODE, $mode);

        return $this;
    }

    /** Use `range` mode (from–to). `false` switches back to `multiple`. */
    public function isRange(bool $range = true): self
    {
        return $this->setDateMode(!$range ? self::DATE_MODE_MULTIPLE : self::DATE_MODE_RANGE);
    }

    /** Separator between dates in `multiple` mode (posted value). Also sets `DateMultipleType` `separator`. */
    public function setConjunction(?string $conjunction): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_CONJUNCTION, $conjunction);
        if ($conjunction !== null) {
            $this->setFormTypeOption('separator', $conjunction);
        }

        return $this;
    }

    /** Separator between the two ends of a `range` selection. Must match `DateMultipleType::SEPARATOR`. */
    public function setRangeSeparator(string $separator = DateMultipleType::SEPARATOR): self
    {
        $this->setHtmlAttribute(self::OPTION_DATE_RANGE_SEPARATOR, $separator);
        $this->setFormTypeOption('separator', $separator);

        return $this;
    }
}
