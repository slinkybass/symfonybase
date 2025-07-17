<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField as EasyField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class PasswordField
{
    use FieldTrait;

    public const OPTION_RENDER_SWITCH = 'renderSwitch';
    public const OPTION_RENDER_GENERATOR = 'renderGenerator';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-password')->onlyOnForms())
            ->isRepeated(false)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function checkIsRepeated(): bool
    {
        return $this->getAsDto()->getFormType() == RepeatedType::class;
    }

    public function isRepeated(bool $repeated = true): self
    {
        if ($repeated) {
            $parentFormType = $this->getAsDto()->getFormType();
            $parentLabel = $this->getAsDto()->getLabel();
            $parentPlaceholder = $this->getAsDto()->getFormTypeOption('attr.' . self::OPTION_PLACEHOLDER);
            $parentMaxLength = $this->getAsDto()->getFormTypeOption('attr.' . self::OPTION_MAX_LENGTH);
            $parentMinLength = $this->getAsDto()->getFormTypeOption('attr.' . self::OPTION_MIN_LENGTH);

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
        } else {
            $this->setFormType(PasswordType::class);
        }

        return $this;
    }

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

    public function setFirstLabel(?string $label): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.label', $label);
        } else {
            $this->getAsDto()->setLabel($label);
        }

        return $this;
    }

    public function setSecondLabel(?string $label): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.label', $label);
        } else {
            $this->getAsDto()->setLabel($label);
        }

        return $this;
    }

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

    public function setFirstPlaceholder(?string $placeholder): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.attr.' . self::OPTION_PLACEHOLDER, $placeholder);
        } else {
            $this->setHtmlAttribute(self::OPTION_PLACEHOLDER, $placeholder);
        }

        return $this;
    }

    public function setSecondPlaceholder(?string $placeholder): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.attr.' . self::OPTION_PLACEHOLDER, $placeholder);
        } else {
            $this->setHtmlAttribute(self::OPTION_PLACEHOLDER, $placeholder);
        }

        return $this;
    }

    public function setMaxLength(?int $maxLength): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.attr.' . self::OPTION_MAX_LENGTH, $maxLength);
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.attr.' . self::OPTION_MAX_LENGTH, $maxLength);
        } else {
            $this->setHtmlAttribute(self::OPTION_MAX_LENGTH, $maxLength);
        }

        return $this;
    }

    public function setMinLength(?int $minLength): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.attr.' . self::OPTION_MIN_LENGTH, $minLength);
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.attr.' . self::OPTION_MIN_LENGTH, $minLength);
        } else {
            $this->setHtmlAttribute(self::OPTION_MIN_LENGTH, $minLength);
        }

        return $this;
    }

    public function renderSwitch(bool $switch = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_SWITCH, $switch);

        return $this;
    }

    public function renderGenerator(bool $generator = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_GENERATOR, $generator);

        return $this;
    }
}
