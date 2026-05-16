<?php

namespace App\Field;

use App\Security\VirtualPermission;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait as EasyTrait;

/**
 * Shared API for custom EasyAdmin fields: syncs the DTO from the wrapped field, applies column defaults, and maps `displayIf()` to `VirtualPermission` for EasyAdmin `setPermission()`.
 */
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

    public const DEFAULT_COLUMNS = 12;

    /**
     * Copies the wrapped field DTO, stamps its FQCN on the DTO, and runs `applyDefaults()` on this object.
     */
    protected function initField(object $field): void
    {
        $this->dto = $field->getAsDto();
        $this->dto->setFieldFqcn($field::class);
        $this->applyDefaults();
    }

    private function applyDefaults(): void
    {
        $this->setDefaultColumns(self::DEFAULT_COLUMNS);
    }

    public function isMapped(bool $mapped = true): self
    {
        $this->setFormTypeOption(self::OPTION_MAPPED, $mapped);

        return $this;
    }

    public function isRequired(bool $required = true): self
    {
        $this->setFormTypeOption(self::OPTION_REQUIRED, $required);
        $this->setHtmlAttribute(self::OPTION_REQUIRED, $required);

        return $this;
    }

    public function isDisabled(bool $disabled = true): self
    {
        $this->setFormTypeOption(self::OPTION_DISABLED, $disabled);

        return $this;
    }

    public function isReadonly(bool $readonly = true): self
    {
        $this->setHtmlAttribute(self::OPTION_READ_ONLY, $readonly);

        return $this;
    }

    public function setData(mixed $data): self
    {
        $this->setFormTypeOption(self::OPTION_DATA, $data);

        return $this;
    }

    public function setPlaceholder(?string $placeholder): self
    {
        $this->setHtmlAttribute(self::OPTION_PLACEHOLDER, $placeholder);

        return $this;
    }

    public function setMaxLength(?int $maxLength): self
    {
        $this->setHtmlAttribute(self::OPTION_MAX_LENGTH, $maxLength);

        return $this;
    }

    public function setMinLength(?int $minLength): self
    {
        $this->setHtmlAttribute(self::OPTION_MIN_LENGTH, $minLength);

        return $this;
    }

    public function isHtml(bool $html = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_AS_HTML, $html);

        return $this;
    }

    public function isSanitized(bool $sanitized = true): self
    {
        $this->setCustomOption(self::OPTION_STRIP_TAGS, $sanitized);

        return $this;
    }

    public function displayIf(bool $visible): self
    {
        $this->setPermission(VirtualPermission::allowed($visible));

        return $this;
    }
}
