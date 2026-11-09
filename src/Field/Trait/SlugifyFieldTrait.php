<?php

namespace App\Field\Trait;

/**
 * slugify options for `SlugField` (`data-slug-*`).
 *
 * Not mapped: per-field `extend` (global char map applied once in JS).
 */
trait SlugifyFieldTrait
{
    public const OPTION_PLUGIN = 'data-slug-field';
    public const OPTION_CONFIRM_TEXT = 'data-slug-confirm-text';
    public const OPTION_REPLACEMENT = 'data-slug-replacement';
    public const OPTION_LOWER = 'data-slug-lower';
    public const OPTION_STRICT = 'data-slug-strict';
    public const OPTION_TRIM = 'data-slug-trim';
    public const OPTION_LOCALE = 'data-slug-locale';
    public const OPTION_REMOVE = 'data-slug-remove';

    /** Load the slugify plugin (`data-slug-field`). `false` keeps a plain text input. */
    public function enablePlugin(bool $enable = true): self
    {
        return $this->enableMappedPlugin('form-type-slug', self::OPTION_PLUGIN, $enable);
    }

    /** Unlock confirmation HTML (`data-slug-confirm-text`); Twig applies `|trans`. */
    public function setConfirmText(string $message): self
    {
        $this->setHtmlAttribute(self::OPTION_CONFIRM_TEXT, $message);

        return $this;
    }

    /** Separator between words (`replacement`). Default `-`. */
    public function setReplacement(string $replacement): self
    {
        $this->setHtmlAttribute(self::OPTION_REPLACEMENT, $replacement);

        return $this;
    }

    /** Lowercase result (`lower`). Default `true`. */
    public function isLower(bool $lower = true): self
    {
        return $this->setDataAttr(self::OPTION_LOWER, $lower);
    }

    /** Strip non-alphanumeric except spaces before replacement (`strict`). Default `true`. */
    public function isStrict(bool $strict = true): self
    {
        return $this->setDataAttr(self::OPTION_STRICT, $strict);
    }

    /** Trim leading/trailing whitespace before replacement (`trim`). Default `true`. */
    public function isTrim(bool $trim = true): self
    {
        return $this->setDataAttr(self::OPTION_TRIM, $trim);
    }

    /**
     * Locale char-map overlay (`locale`).
     * Built-ins: bg, de, es, fr, pt, uk, vi, da, nb, it, nl, sv.
     */
    public function setLocale(string $locale): self
    {
        $this->setHtmlAttribute(self::OPTION_LOCALE, $locale);

        return $this;
    }

    /**
     * Characters to strip before strict/replacement (`remove`).
     * Value is a RegExp source string (no delimiters), e.g. `[^A-Za-z0-9\\s-]`.
     */
    public function setRemove(string $pattern): self
    {
        $this->setHtmlAttribute(self::OPTION_REMOVE, $pattern);

        return $this;
    }
}
