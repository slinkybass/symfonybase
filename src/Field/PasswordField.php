<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\PasswordFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField as EasyField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `TextField` wrapper with password visibility and generator options. */
class PasswordField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use PasswordFieldTrait;

    private EasyField $innerField;

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        $field = new self();
        $field->innerField = EasyField::new($propertyName, $label);
        $field->initField($field->innerField);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
        $this->enablePlugin();
        $this->isRepeated(false);
    }

    /** Whether the field asks for the password twice (`RepeatedType`). */
    public function checkIsRepeated(): bool
    {
        return $this->getAsDto()->getFormType() == RepeatedType::class;
    }

    /** Ask for the password twice. `false` keeps a single password input. */
    public function isRepeated(bool $repeated = true): self
    {
        if ($repeated) {
            $parentFormType = $this->getAsDto()->getFormType();
            $parentLabel = $this->getAsDto()->getLabel();
            $parentPlaceholder = $this->getAsDto()->getFormTypeOption('attr.'.self::OPTION_PLACEHOLDER);
            $parentMaxLength = $this->getAsDto()->getFormTypeOption('attr.'.self::OPTION_MAX_LENGTH);
            $parentMinLength = $this->getAsDto()->getFormTypeOption('attr.'.self::OPTION_MIN_LENGTH);

            $this->setFormType(RepeatedType::class);
            $this->setFormTypeOption('type', $parentFormType);

            if ($parentLabel) {
                $this->setFirstLabel($parentLabel);
                $this->setSecondLabel($parentLabel);
            }
            if ($parentPlaceholder) {
                $this->setFirstPlaceholder($parentPlaceholder);
                $this->setSecondPlaceholder($parentPlaceholder);
            }
            if ($parentMaxLength) {
                $this->setFirstMaxLength($parentMaxLength);
                $this->setSecondMaxLength($parentMaxLength);
            }
            if ($parentMinLength) {
                $this->setFirstMinLength($parentMinLength);
                $this->setSecondMinLength($parentMinLength);
            }
            $this->adoptPasswordAttrsForRepeated();
        } else {
            $this->setFormType(PasswordType::class);
        }

        return $this;
    }

    /** Label of the field, applied to both inputs when the password is repeated. */
    public function setLabel(?string $label): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFirstLabel($label);
            $this->setSecondLabel($label);
        } else {
            $this->getAsDto()->setLabel($label);
        }

        return $this;
    }

    /** Label of the first input when the password is repeated. */
    public function setFirstLabel(?string $label): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.label', $label);
        } else {
            $this->getAsDto()->setLabel($label);
        }

        return $this;
    }

    /** Label of the confirmation input when the password is repeated. */
    public function setSecondLabel(?string $label): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.label', $label);
        } else {
            $this->getAsDto()->setLabel($label);
        }

        return $this;
    }

    /** HTML `maxlength` of the first input when the password is repeated. */
    public function setFirstMaxLength(?int $length): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.attr.'.self::OPTION_MAX_LENGTH, $length);
        } else {
            $this->setMaxLength($length);
        }

        return $this;
    }

    /** HTML `maxlength` of the confirmation input when the password is repeated. */
    public function setSecondMaxLength(?int $length): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.attr.'.self::OPTION_MAX_LENGTH, $length);
        } else {
            $this->setMaxLength($length);
        }

        return $this;
    }

    /** HTML `minlength` of the first input when the password is repeated. */
    public function setFirstMinLength(?int $length): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.attr.'.self::OPTION_MIN_LENGTH, $length);
        } else {
            $this->setMaxLength($length);
        }

        return $this;
    }

    /** HTML `minlength` of the confirmation input when the password is repeated. */
    public function setSecondMinLength(?int $length): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.attr.'.self::OPTION_MIN_LENGTH, $length);
        } else {
            $this->setMaxLength($length);
        }

        return $this;
    }

    /** Placeholder on the input, or on both inputs when the password is repeated. */
    public function setPlaceholder(?string $placeholder): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFirstPlaceholder($placeholder);
            $this->setSecondPlaceholder($placeholder);
        } else {
            $this->setHtmlAttribute(self::OPTION_PLACEHOLDER, $placeholder);
        }

        return $this;
    }

    /** Placeholder of the first input when the password is repeated. */
    public function setFirstPlaceholder(?string $placeholder): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.attr.'.self::OPTION_PLACEHOLDER, $placeholder);
        } else {
            $this->setHtmlAttribute(self::OPTION_PLACEHOLDER, $placeholder);
        }

        return $this;
    }

    /** Placeholder of the confirmation input when the password is repeated. */
    public function setSecondPlaceholder(?string $placeholder): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.attr.'.self::OPTION_PLACEHOLDER, $placeholder);
        } else {
            $this->setHtmlAttribute(self::OPTION_PLACEHOLDER, $placeholder);
        }

        return $this;
    }

    /** HTML `maxlength` on the input, or on both inputs when the password is repeated. */
    public function setMaxLength(?int $maxLength): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.attr.'.self::OPTION_MAX_LENGTH, $maxLength);
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.attr.'.self::OPTION_MAX_LENGTH, $maxLength);
        } else {
            $this->setHtmlAttribute(self::OPTION_MAX_LENGTH, $maxLength);
        }

        return $this;
    }

    /** HTML `minlength` on the input, or on both inputs when the password is repeated. */
    public function setMinLength(?int $minLength): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.attr.'.self::OPTION_MIN_LENGTH, $minLength);
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.attr.'.self::OPTION_MIN_LENGTH, $minLength);
        } else {
            $this->setHtmlAttribute(self::OPTION_MIN_LENGTH, $minLength);
        }

        return $this;
    }
}
