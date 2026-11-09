<?php

namespace App\Field\Trait;

/**
 * noUiSlider options for `FloatField` / `IntegerField` (`data-slider-*`).
 *
 * Range/start/step still use native `min` / `max` / `step` / value.
 * Not mapped: format/ariaFormat callables; multi-handle starts; cssPrefix/cssClasses;
 * documentElement; handleAttributes; pips format functions.
 */
trait NoUiSliderFieldTrait
{
    public const OPTION_PLUGIN_SLIDER = 'data-slider-field';

    public const OPTION_SLIDER_SHOW_INPUT = 'data-slider-show-input';
    public const OPTION_SLIDER_TOOLTIPS = 'data-slider-tooltips';
    public const OPTION_SLIDER_CONNECT = 'data-slider-connect';
    public const OPTION_SLIDER_PIPS = 'data-slider-pips';
    public const OPTION_SLIDER_ORIENTATION = 'data-slider-orientation';
    public const OPTION_SLIDER_DIRECTION = 'data-slider-direction';
    public const OPTION_SLIDER_BEHAVIOUR = 'data-slider-behaviour';
    public const OPTION_SLIDER_ANIMATE = 'data-slider-animate';
    public const OPTION_SLIDER_ANIMATION_DURATION = 'data-slider-animation-duration';
    public const OPTION_SLIDER_KEYBOARD_SUPPORT = 'data-slider-keyboard-support';
    public const OPTION_SLIDER_KEYBOARD_PAGE_MULTIPLIER = 'data-slider-keyboard-page-multiplier';
    public const OPTION_SLIDER_KEYBOARD_MULTIPLIER = 'data-slider-keyboard-multiplier';
    public const OPTION_SLIDER_KEYBOARD_DEFAULT_STEP = 'data-slider-keyboard-default-step';
    public const OPTION_SLIDER_MARGIN = 'data-slider-margin';
    public const OPTION_SLIDER_LIMIT = 'data-slider-limit';
    public const OPTION_SLIDER_PADDING = 'data-slider-padding';
    public const OPTION_SLIDER_SNAP = 'data-slider-snap';
    public const OPTION_SLIDER_DECIMALS = 'data-slider-decimals';
    public const OPTION_SLIDER_HEIGHT = 'data-slider-height';

    public const SLIDER_CONNECT_UPPER = 'upper';
    public const SLIDER_CONNECT_LOWER = 'lower';
    public const SLIDER_ORIENTATION_HORIZONTAL = 'horizontal';
    public const SLIDER_ORIENTATION_VERTICAL = 'vertical';
    public const SLIDER_DIRECTION_LTR = 'ltr';
    public const SLIDER_DIRECTION_RTL = 'rtl';

    /** Load noUiSlider (`data-slider-field`). */
    public function isSlider(bool $enable = true): self
    {
        return $this->enableMappedPlugin('form-type-slider', self::OPTION_PLUGIN_SLIDER, $enable);
    }

    /** Keep the backing number input visible. */
    public function sliderShowInput(bool $show = true): self
    {
        return $this->setDataAttr(self::OPTION_SLIDER_SHOW_INPUT, $show);
    }

    /** Handle value tooltips. */
    public function sliderShowTooltip(bool $tooltip = true): self
    {
        return $this->setDataAttr(self::OPTION_SLIDER_TOOLTIPS, $tooltip);
    }

    /**
     * Connect bar (`connect`).
     *
     * @param self::SLIDER_CONNECT_*|bool|list<bool> $connect
     */
    public function setSliderConnect(string|bool|array $connect): self
    {
        if (\is_bool($connect) || \is_array($connect)) {
            return $this->setDataAttr(self::OPTION_SLIDER_CONNECT, $connect);
        }
        $this->setHtmlAttribute(self::OPTION_SLIDER_CONNECT, $connect);

        return $this;
    }

    public function sliderConnectUpper(): self
    {
        return $this->setSliderConnect(self::SLIDER_CONNECT_UPPER);
    }

    public function sliderConnectLower(): self
    {
        return $this->setSliderConnect(self::SLIDER_CONNECT_LOWER);
    }

    /**
     * Scale marks. `true` enables steps mode; pass an array for a full pips config.
     *
     * @param bool|array<string, mixed> $pips
     */
    public function sliderPips(bool|array $pips = true): self
    {
        if (\is_array($pips)) {
            return $this->setDataAttr(self::OPTION_SLIDER_PIPS, $pips);
        }

        return $this->setDataAttr(self::OPTION_SLIDER_PIPS, $pips);
    }

    /**
     * @param self::SLIDER_ORIENTATION_* $orientation
     */
    public function setSliderOrientation(string $orientation): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_ORIENTATION, $orientation);

        return $this;
    }

    /**
     * @param self::SLIDER_DIRECTION_* $direction
     */
    public function setSliderDirection(string $direction): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_DIRECTION, $direction);

        return $this;
    }

    /**
     * Interaction mode (`behaviour`, hyphen-separated).
     * Default includes `tap` (click the bar to move). Examples: `drag` (handle only),
     * `tap-snap` (tap jumps), `none` (no interaction).
     */
    public function setSliderBehaviour(string $behaviour): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_BEHAVIOUR, $behaviour);

        return $this;
    }

    public function isSliderAnimate(bool $animate = true): self
    {
        return $this->setDataAttr(self::OPTION_SLIDER_ANIMATE, $animate);
    }

    public function setSliderAnimationDuration(int $ms): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_ANIMATION_DURATION, (string) $ms);

        return $this;
    }

    public function isSliderKeyboardSupport(bool $enable = true): self
    {
        return $this->setDataAttr(self::OPTION_SLIDER_KEYBOARD_SUPPORT, $enable);
    }

    public function setSliderKeyboardPageMultiplier(float|int $multiplier): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_KEYBOARD_PAGE_MULTIPLIER, (string) $multiplier);

        return $this;
    }

    public function setSliderKeyboardMultiplier(float|int $multiplier): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_KEYBOARD_MULTIPLIER, (string) $multiplier);

        return $this;
    }

    public function setSliderKeyboardDefaultStep(float|int $step): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_KEYBOARD_DEFAULT_STEP, (string) $step);

        return $this;
    }

    /** Minimum distance between handles (multi-handle). */
    public function setSliderMargin(float|int $margin): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_MARGIN, (string) $margin);

        return $this;
    }

    /** Maximum distance between handles (multi-handle). */
    public function setSliderLimit(float|int $limit): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_LIMIT, (string) $limit);

        return $this;
    }

    /**
     * @param float|int|list<float|int> $padding
     */
    public function setSliderPadding(float|int|array $padding): self
    {
        if (\is_array($padding)) {
            return $this->setDataAttr(self::OPTION_SLIDER_PADDING, $padding);
        }
        $this->setHtmlAttribute(self::OPTION_SLIDER_PADDING, (string) $padding);

        return $this;
    }

    /**
     * Snap handle to listed values (`snap`), unlike `setStep` which only quantizes a linear drag.
     *
     * - `true`: build even stops from min/max/step
     * - `list<int|float>`: only those values (e.g. `[0, 25, 70, 100]`)
     *
     * @param bool|list<int|float> $snap
     */
    public function isSliderSnap(bool|array $snap = true): self
    {
        if (\is_array($snap)) {
            return $this->setDataAttr(self::OPTION_SLIDER_SNAP, array_values($snap));
        }

        return $this->setDataAttr(self::OPTION_SLIDER_SNAP, $snap);
    }

    /** Decimals used by the slider value formatter. */
    public function setSliderDecimals(int $decimals): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_DECIMALS, (string) $decimals);

        return $this;
    }

    /** CSS height for vertical sliders (e.g. `200px`). */
    public function setSliderHeight(string $height): self
    {
        $this->setHtmlAttribute(self::OPTION_SLIDER_HEIGHT, $height);

        return $this;
    }
}
