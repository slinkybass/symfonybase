<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait as EasyTrait;

trait FieldTrait
{
    use EasyTrait;

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
    public const OPTION_HIDDEN = 'NOPERMISSION_FIELD';

    protected function initField(object $field): void
    {
        $this->dto = $field->getAsDto();
        $this->dto->setFieldFqcn($field::class);
        $this->applyDefaults();
    }

    private function applyDefaults(): void
    {
        $this->setDefaultColumns(12);
    }

    public function isMapped(bool $val = true): self
    {
        $this->setFormTypeOption(self::OPTION_MAPPED, $val);

        return $this;
    }

    public function isRequired(bool $val = true): self
    {
        $this->setFormTypeOption(self::OPTION_REQUIRED, $val);
        $this->setHtmlAttribute(self::OPTION_REQUIRED, $val);

        return $this;
    }

    public function isDisabled(bool $val = true): self
    {
        $this->setFormTypeOption(self::OPTION_DISABLED, $val);

        return $this;
    }

    public function isReadonly(bool $val = true): self
    {
        $this->setHtmlAttribute(self::OPTION_READ_ONLY, $val);

        return $this;
    }

    public function setData(mixed $val): self
    {
        $this->setFormTypeOption(self::OPTION_DATA, $val);

        return $this;
    }

    public function setPlaceholder(?string $val): self
    {
        $this->setHtmlAttribute(self::OPTION_PLACEHOLDER, $val);

        return $this;
    }

    public function setMaxLength(?int $val): self
    {
        $this->setHtmlAttribute(self::OPTION_MAX_LENGTH, $val);

        return $this;
    }

    public function setMinLength(?int $val): self
    {
        $this->setHtmlAttribute(self::OPTION_MIN_LENGTH, $val);

        return $this;
    }

    public function isHtml(bool $val = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_AS_HTML, $val);

        return $this;
    }

    public function isSanitized(bool $val = true): self
    {
        $this->setCustomOption(self::OPTION_STRIP_TAGS, $val);

        return $this;
    }

    public function displayIf(bool $val): self
    {
        $this->setPermission($val ? '' : self::OPTION_HIDDEN);

        return $this;
    }
}
