<?php

namespace App\Field;

trait FieldTrait
{
    public const OPTION_MAPPED = 'mapped';
    public const OPTION_REQUIRED = 'required';
    public const OPTION_DISABLED = 'disabled';
    public const OPTION_READ_ONLY = 'readonly';
    public const OPTION_DATA = 'data';
    public const OPTION_PLACEHOLDER = 'placeholder';
    public const OPTION_MAX_LENGTH = 'maxlength';
    public const OPTION_MIN_LENGTH = 'minlength';
    public const OPTION_RENDER_AS_HTML = 'renderAsHtml';
    public const OPTION_STRIP_TAGS = 'stripTags';

    public function __call(string $method, array $args)
    {
        $result = $this->field->$method(...$args);

        if ($result === $this->field) {
            return $this;
        }

        return $result;
    }

    public function getField(): object
    {
        return $this->field;
    }

    public function isMapped(bool $val = true): self
    {
        $this->field->setFormTypeOption(self::OPTION_MAPPED, $val);

        return $this;
    }

    public function isRequired(bool $val = true): self
    {
        $this->field->setFormTypeOption(self::OPTION_REQUIRED, $val);

        return $this;
    }

    public function isDisabled(bool $val = true): self
    {
        $this->field->setFormTypeOption(self::OPTION_DISABLED, $val);

        return $this;
    }

    public function isReadonly(bool $val = true): self
    {
        $this->field->setHtmlAttribute(self::OPTION_READ_ONLY, $val);

        return $this;
    }

    public function setData(mixed $val): self
    {
        $this->field->setFormTypeOption(self::OPTION_DATA, $val);

        return $this;
    }

    public function setPlaceholder(?string $val): self
    {
        $this->field->setHtmlAttribute(self::OPTION_PLACEHOLDER, $val);

        return $this;
    }

    public function setMaxLength(?int $val): self
    {
        $this->field->setHtmlAttribute(self::OPTION_MAX_LENGTH, $val);

        return $this;
    }

    public function setMinLength(?int $val): self
    {
        $this->field->setHtmlAttribute(self::OPTION_MIN_LENGTH, $val);

        return $this;
    }

    public function renderAsHtml(bool $val = true): self
    {
        $this->field->setCustomOption(self::OPTION_RENDER_AS_HTML, $val);

        return $this;
    }

    public function stripTags(bool $val = true): self
    {
        $this->field->setCustomOption(self::OPTION_STRIP_TAGS, $val);

        return $this;
    }
}
