<?php

namespace App\Field\Trait;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;

/**
 * IMask options for `MaskField` (`data-mask-*`).
 *
 * Named presets cover prepare/commit (not arbitrary JS callbacks).
 * Not mapped: validate/format/parse functions, blocks, Function/Dynamic/Enum/Date masks.
 */
trait IMaskFieldTrait
{
    public const OPTION_PLUGIN = 'data-mask-field';

    public const OPTION_MASK_PATTERN = 'data-mask-pattern';
    public const OPTION_MASK_REGEX = 'data-mask-regex';
    public const OPTION_MASK_TYPE = 'data-mask-type';
    public const OPTION_MASK_LAZY = 'data-mask-lazy';
    public const OPTION_MASK_PLACEHOLDER = 'data-mask-placeholder';
    public const OPTION_MASK_DISPLAY_CHAR = 'data-mask-display-char';
    public const OPTION_MASK_OVERWRITE = 'data-mask-overwrite';
    public const OPTION_MASK_EAGER = 'data-mask-eager';
    public const OPTION_MASK_SKIP_INVALID = 'data-mask-skip-invalid';
    public const OPTION_MASK_AUTOFIX = 'data-mask-autofix';
    public const OPTION_MASK_UPPERCASE = 'data-mask-uppercase';
    public const OPTION_MASK_PREPARE = 'data-mask-prepare';
    public const OPTION_MASK_COMMIT = 'data-mask-commit';
    public const OPTION_MASK_UNMASK = 'data-mask-unmask';
    public const OPTION_MASK_DEFINITIONS = 'data-mask-definitions';
    public const OPTION_MASK_SCALE = 'data-mask-scale';
    public const OPTION_MASK_RADIX = 'data-mask-radix';
    public const OPTION_MASK_THOUSANDS_SEPARATOR = 'data-mask-thousands-separator';
    public const OPTION_MASK_PAD_FRACTIONAL_ZEROS = 'data-mask-pad-fractional-zeros';
    public const OPTION_MASK_NORMALIZE_ZEROS = 'data-mask-normalize-zeros';
    public const OPTION_MASK_MIN = 'data-mask-min';
    public const OPTION_MASK_MAX = 'data-mask-max';
    public const OPTION_MASK_MAP_TO_RADIX = 'data-mask-map-to-radix';

    /** types */
    public const MASK_TYPE_PATTERN = 'pattern';
    public const MASK_TYPE_NUMBER = 'number';

    /** overwrite */
    public const MASK_OVERWRITE_SHIFT = 'shift';

    /** eager */
    public const MASK_EAGER_APPEND = 'append';
    public const MASK_EAGER_REMOVE = 'remove';

    /** autofix */
    public const MASK_AUTOFIX_PAD = 'pad';

    /** prepare presets (`data-mask-prepare`, CSV) */
    public const PREPARE_UPPER = 'upper';
    public const PREPARE_LOWER = 'lower';
    public const PREPARE_TRIM = 'trim';
    public const PREPARE_STRIP_SPACES = 'strip-spaces';
    public const PREPARE_DIGITS_ONLY = 'digits-only';

    /** commit presets (`data-mask-commit`, CSV) */
    public const COMMIT_TRIM = 'trim';
    public const COMMIT_PAD = 'pad';

    protected function enableMappedPlugin(string $entry, string $flagAttr, bool $enable): self
    {
        $this->dto->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new($entry)->onlyOnForms());
        }
        $this->setHtmlAttribute($flagAttr, json_encode($enable));

        return $this;
    }

    protected function setMaskBoolAttr(string $option, bool $value): self
    {
        $this->setHtmlAttribute($option, json_encode($value));

        return $this;
    }

    protected function setMaskJsonAttr(string $option, mixed $value): self
    {
        $this->setHtmlAttribute($option, json_encode($value));

        return $this;
    }

    /**
     * @param string|list<string> $presets
     *
     * @return list<string>
     */
    private function normalizePresetList(string|array $presets): array
    {
        if (\is_array($presets)) {
            $list = $presets;
        } else {
            $list = preg_split('/\s*,\s*/', $presets);
            $list = false === $list ? [] : $list;
        }

        return array_values(array_unique(array_filter(array_map('strval', $list), static fn (string $p): bool => '' !== $p)));
    }

    /**
     * Mask kind. `number` uses IMask `Number`; pattern/regex use `setPattern()`.
     *
     * @param self::MASK_TYPE_*|string $type
     */
    public function setMaskType(string $type): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_TYPE, $type);

        return $this;
    }

    /** Treat `setPattern()` as a RegExp source (`data-mask-regex`). */
    public function isRegex(bool $regex = true): self
    {
        return $this->setMaskBoolAttr(self::OPTION_MASK_REGEX, $regex);
    }

    /** IMask pattern or RegExp source (`data-mask-pattern`). Required unless type is number. */
    public function setPattern(string $pattern): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_PATTERN, $pattern);

        return $this;
    }

    /**
     * IMask `lazy`: when true, empty slots never show `placeholderChar` (only fixed mask chars appear as filled).
     * When omitted and a placeholder is set, defaults to `lazy: false` (guide always visible).
     */
    public function isLazy(bool $lazy = true): self
    {
        return $this->setMaskBoolAttr(self::OPTION_MASK_LAZY, $lazy);
    }

    /** Placeholder character (`data-mask-placeholder`). */
    public function setMaskPlaceholder(string $placeholder): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_PLACEHOLDER, $placeholder);

        return $this;
    }

    /** Display character for empty slots (`data-mask-display-char`). */
    public function setDisplayChar(string $displayChar): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_DISPLAY_CHAR, $displayChar);

        return $this;
    }

    /**
     * IMask `overwrite` (`true` / `false` / `shift`).
     *
     * @param bool|self::MASK_OVERWRITE_*|string $overwrite
     */
    public function setOverwrite(bool|string $overwrite = true): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_OVERWRITE, is_bool($overwrite) ? json_encode($overwrite) : $overwrite);

        return $this;
    }

    /** Boolean shortcut for `setOverwrite(true|false)`. */
    public function isOverwrite(bool $overwrite = true): self
    {
        return $this->setOverwrite($overwrite);
    }

    /**
     * IMask `eager` (`true` / `false` / `append` / `remove`).
     *
     * @param bool|self::MASK_EAGER_*|string $eager
     */
    public function setEager(bool|string $eager = true): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_EAGER, is_bool($eager) ? json_encode($eager) : $eager);

        return $this;
    }

    /** Skip keystrokes that do not match the mask (`data-mask-skip-invalid`). */
    public function isSkipInvalid(bool $skip = true): self
    {
        return $this->setMaskBoolAttr(self::OPTION_MASK_SKIP_INVALID, $skip);
    }

    /**
     * IMask `autofix` (`true` / `false` / `pad`).
     *
     * @param bool|self::MASK_AUTOFIX_*|string $autofix
     */
    public function setAutofix(bool|string $autofix = true): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_AUTOFIX, is_bool($autofix) ? json_encode($autofix) : $autofix);

        return $this;
    }

    /**
     * Named prepare presets (CSV). Applied before the mask accepts input.
     *
     * @param self::PREPARE_*|string|list<self::PREPARE_*|string> $prepare
     */
    public function setPrepare(string|array $prepare): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_PREPARE, implode(',', $this->normalizePresetList($prepare)));

        return $this;
    }

    /** Force typed letters to uppercase (`prepare` preset `upper`). */
    public function isUppercase(bool $uppercase = true): self
    {
        return $this->setMaskBoolAttr(self::OPTION_MASK_UPPERCASE, $uppercase);
    }

    /** Force typed letters to lowercase (`prepare` preset `lower`). */
    public function isLowercase(bool $lowercase = true): self
    {
        return $lowercase
            ? $this->setPrepare(self::PREPARE_LOWER)
            : $this;
    }

    /**
     * Named commit presets (CSV). Run when leaving the field.
     *
     * @param self::COMMIT_*|string|list<self::COMMIT_*|string> $commit
     */
    public function setCommit(string|array $commit): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_COMMIT, implode(',', $this->normalizePresetList($commit)));

        return $this;
    }

    /** On submit, post `unmaskedValue` (no fixed separators) instead of the display value. */
    public function isUnmask(bool $unmask = true): self
    {
        return $this->setMaskBoolAttr(self::OPTION_MASK_UNMASK, $unmask);
    }

    /**
     * Custom pattern definitions (`char` → RegExp source string).
     *
     * @param array<string, string>|null $definitions
     */
    public function setDefinitions(?array $definitions): self
    {
        return $this->setMaskJsonAttr(self::OPTION_MASK_DEFINITIONS, $definitions ?? []);
    }

    /** Fractional digits for Number mask (`data-mask-scale`). */
    public function setScale(int $scale): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_SCALE, $scale);

        return $this;
    }

    /** Decimal separator for Number mask (`data-mask-radix`). */
    public function setRadix(string $radix): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_RADIX, $radix);

        return $this;
    }

    /** Thousands separator for Number mask (`data-mask-thousands-separator`). */
    public function setThousandsSeparator(string $separator): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_THOUSANDS_SEPARATOR, $separator);

        return $this;
    }

    /** Pad fractional zeros to `scale` length. */
    public function isPadFractionalZeros(bool $pad = true): self
    {
        return $this->setMaskBoolAttr(self::OPTION_MASK_PAD_FRACTIONAL_ZEROS, $pad);
    }

    /** Normalize leading/trailing zeros on Number mask. */
    public function isNormalizeZeros(bool $normalize = true): self
    {
        return $this->setMaskBoolAttr(self::OPTION_MASK_NORMALIZE_ZEROS, $normalize);
    }

    /** Minimum numeric value (`data-mask-min`). */
    public function setMaskMin(int|float $min): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_MIN, $min);

        return $this;
    }

    /** Maximum numeric value (`data-mask-max`). */
    public function setMaskMax(int|float $max): self
    {
        $this->setHtmlAttribute(self::OPTION_MASK_MAX, $max);

        return $this;
    }

    /**
     * Extra characters treated as radix (`data-mask-map-to-radix`, JSON array).
     *
     * @param list<string>|null $mapToRadix
     */
    public function setMapToRadix(?array $mapToRadix): self
    {
        return $this->setMaskJsonAttr(self::OPTION_MASK_MAP_TO_RADIX, $mapToRadix ?? []);
    }
}
