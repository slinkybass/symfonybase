<?php

namespace App\Field\Trait;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;

/**
 * SignaturePad options for `SignatureField` (`data-signature-*`).
 *
 * Not mapped: `dotSize` as a function; stroke event hooks; per-stroke point-group overrides.
 */
trait SignatureFieldTrait
{
    public const OPTION_PLUGIN = 'data-signature-field';

    public const OPTION_SHOW_INPUT = 'data-signature-show-input';
    public const OPTION_SHOW_UNDO = 'data-signature-show-undo';
    public const OPTION_SHOW_CLEAR = 'data-signature-show-clear';

    public const OPTION_PEN_COLOR = 'data-signature-pen-color';
    public const OPTION_BACKGROUND_COLOR = 'data-signature-background-color';
    public const OPTION_DOT_SIZE = 'data-signature-dot-size';
    public const OPTION_MIN_WIDTH = 'data-signature-min-width';
    public const OPTION_MAX_WIDTH = 'data-signature-max-width';
    public const OPTION_THROTTLE = 'data-signature-throttle';
    public const OPTION_MIN_DISTANCE = 'data-signature-min-distance';
    public const OPTION_VELOCITY_FILTER_WEIGHT = 'data-signature-velocity-filter-weight';
    public const OPTION_COMPOSITE_OPERATION = 'data-signature-composite-operation';
    public const OPTION_CANVAS_CONTEXT_OPTIONS = 'data-signature-canvas-context-options';
    public const OPTION_FORMAT = 'data-signature-format';
    public const OPTION_QUALITY = 'data-signature-quality';

    public const FORMAT_PNG = 'image/png';
    public const FORMAT_JPEG = 'image/jpeg';
    public const FORMAT_SVG = 'image/svg+xml';

    protected function enableMappedPlugin(string $entry, string $flagAttr, bool $enable): self
    {
        $this->dto->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new($entry)->onlyOnForms());
        }
        $this->setHtmlAttribute($flagAttr, json_encode($enable));

        return $this;
    }

    protected function setSignatureBoolAttr(string $option, bool $value): self
    {
        $this->setHtmlAttribute($option, json_encode($value));

        return $this;
    }

    protected function setSignatureJsonAttr(string $option, mixed $value): self
    {
        $this->setHtmlAttribute($option, json_encode($value));

        return $this;
    }

    /** Keep the backing input visible (`data-signature-show-input`). */
    public function showInput(bool $show = true): self
    {
        return $this->setSignatureBoolAttr(self::OPTION_SHOW_INPUT, $show);
    }

    /** Undo button (`data-signature-show-undo`). */
    public function showUndo(bool $show = true): self
    {
        return $this->setSignatureBoolAttr(self::OPTION_SHOW_UNDO, $show);
    }

    /** Clear button (`data-signature-show-clear`). */
    public function showClear(bool $show = true): self
    {
        return $this->setSignatureBoolAttr(self::OPTION_SHOW_CLEAR, $show);
    }

    /** Stroke color (`penColor`). */
    public function setPenColor(string $color): self
    {
        $this->setHtmlAttribute(self::OPTION_PEN_COLOR, $color);

        return $this;
    }

    /**
     * Clear / fill background (`backgroundColor`).
     * Use an opaque color (e.g. `rgb(255,255,255)`) when exporting JPEG.
     */
    public function setBackgroundColor(string $color): self
    {
        $this->setHtmlAttribute(self::OPTION_BACKGROUND_COLOR, $color);

        return $this;
    }

    /** Single-dot radius (`dotSize`). `0` uses (minWidth+maxWidth)/2. */
    public function setDotSize(float $size): self
    {
        $this->setHtmlAttribute(self::OPTION_DOT_SIZE, (string) $size);

        return $this;
    }

    /** Minimum stroke width (`minWidth`). */
    public function setMinWidth(float $width): self
    {
        $this->setHtmlAttribute(self::OPTION_MIN_WIDTH, (string) $width);

        return $this;
    }

    /** Maximum stroke width (`maxWidth`). */
    public function setMaxWidth(float $width): self
    {
        $this->setHtmlAttribute(self::OPTION_MAX_WIDTH, (string) $width);

        return $this;
    }

    /** Draw at most once per N ms (`throttle`). `0` disables throttling. */
    public function setThrottle(int $ms): self
    {
        $this->setHtmlAttribute(self::OPTION_THROTTLE, (string) $ms);

        return $this;
    }

    /** Minimum distance in px between points (`minDistance`). */
    public function setMinDistance(int $px): self
    {
        $this->setHtmlAttribute(self::OPTION_MIN_DISTANCE, (string) $px);

        return $this;
    }

    /** Velocity smoothing weight (`velocityFilterWeight`). */
    public function setVelocityFilterWeight(float $weight): self
    {
        $this->setHtmlAttribute(self::OPTION_VELOCITY_FILTER_WEIGHT, (string) $weight);

        return $this;
    }

    /**
     * Canvas `globalCompositeOperation` (`compositeOperation`).
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/globalCompositeOperation
     */
    public function setCompositeOperation(string $operation): self
    {
        $this->setHtmlAttribute(self::OPTION_COMPOSITE_OPERATION, $operation);

        return $this;
    }

    /**
     * `getContext('2d', …)` options (`canvasContextOptions`).
     *
     * @param array<string, mixed> $options
     */
    public function setCanvasContextOptions(array $options): self
    {
        return $this->setSignatureJsonAttr(self::OPTION_CANVAS_CONTEXT_OPTIONS, $options);
    }

    /**
     * `toDataURL` MIME type (`data-signature-format`).
     *
     * @param self::FORMAT_* $format
     */
    public function setFormat(string $format): self
    {
        $this->setHtmlAttribute(self::OPTION_FORMAT, $format);

        return $this;
    }

    /** JPEG quality 0–1 (`data-signature-quality`). Ignored for PNG/SVG. */
    public function setQuality(float $quality): self
    {
        $this->setHtmlAttribute(self::OPTION_QUALITY, (string) $quality);

        return $this;
    }
}
