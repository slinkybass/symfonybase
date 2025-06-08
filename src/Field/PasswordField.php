<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
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
        $instance->field->getAsDto()->setAssets(new AssetsDto());

        $instance
            ->addAssetMapperEntries(Asset::new('form-type-password')->onlyOnForms())
            ->repeated(false)
            ->renderSwitch(true)
            ->renderGenerator(true)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function isRepeated(): bool
    {
        return $this->getAsDto()->getFormType() == RepeatedType::class;
    }

    public function repeated(bool $val = true): self
    {
        if ($val) {
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
        if ($this->isRepeated()) {
            $this->setFirstLabel($label);
            $this->setSecondLabel($label);
        } else {
            $this->getAsDto()->setLabel($label);
        }

        return $this;
    }

    public function setFirstLabel(?string $val): self
    {
        if ($this->isRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.label', $val);
        } else {
            $this->getAsDto()->setLabel($val);
        }

        return $this;
    }

    public function setSecondLabel(?string $val): self
    {
        if ($this->isRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.label', $val);
        } else {
            $this->getAsDto()->setLabel($val);
        }

        return $this;
    }

    public function setPlaceholder(?string $val): self
    {
        if ($this->isRepeated()) {
            $this->setFirstPlaceholder($val);
            $this->setSecondPlaceholder($val);
        } else {
            $this->setHtmlAttribute(self::OPTION_PLACEHOLDER, $val);
        }

        return $this;
    }

    public function setFirstPlaceholder(?string $val): self
    {
        if ($this->isRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.attr.' . self::OPTION_PLACEHOLDER, $val);
        } else {
            $this->setHtmlAttribute(self::OPTION_PLACEHOLDER, $val);
        }

        return $this;
    }

    public function setSecondPlaceholder(?string $val): self
    {
        if ($this->isRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.attr.' . self::OPTION_PLACEHOLDER, $val);
        } else {
            $this->setHtmlAttribute(self::OPTION_PLACEHOLDER, $val);
        }

        return $this;
    }

    public function setMaxLength(?int $val): self
    {
        if ($this->isRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.attr.' . self::OPTION_MAX_LENGTH, $val);
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.attr.' . self::OPTION_MAX_LENGTH, $val);
        } else {
            $this->setHtmlAttribute(self::OPTION_MAX_LENGTH, $val);
        }

        return $this;
    }

    public function setMinLength(?int $val): self
    {
        if ($this->isRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.attr.' . self::OPTION_MIN_LENGTH, $val);
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.attr.' . self::OPTION_MIN_LENGTH, $val);
        } else {
            $this->setHtmlAttribute(self::OPTION_MIN_LENGTH, $val);
        }

        return $this;
    }

    public function renderSwitch(bool $val = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_SWITCH, $val);

        return $this;
    }

    public function renderGenerator(bool $val = true): self
    {
        $this->setCustomOption(self::OPTION_RENDER_GENERATOR, $val);

        return $this;
    }
}
