<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
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

    private EasyField $innerField;

    public const OPTION_PLUGIN = 'data-password-field';
    public const OPTION_SWITCH = 'data-password-switch';
    public const OPTION_GENERATOR = 'data-password-generator';

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

    /** Load the password plugin (`data-password-field`). `false` keeps a plain password input. */
    public function enablePlugin(bool $enable = true): self
    {
        $this->dto->setAssets(new AssetsDto());
        if ($enable) {
            $this->addAssetMapperEntries(Asset::new('form-type-password')->onlyOnForms());
        }

        return $this->setPasswordAttr(self::OPTION_PLUGIN, json_encode($enable));
    }

    /** Eye button (`data-password-switch`). */
    public function renderSwitch(bool $switch = true): self
    {
        return $this->setPasswordAttr(self::OPTION_SWITCH, json_encode($switch));
    }

    /** Generator button (`data-password-generator`). */
    public function renderGenerator(bool $generator = true): self
    {
        return $this->setPasswordAttr(self::OPTION_GENERATOR, json_encode($generator));
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
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.label', $label);
        } else {
            $this->getAsDto()->setLabel($label);
        }

        return $this;
    }

    public function setSecondLabel(?string $label): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.label', $label);
        } else {
            $this->getAsDto()->setLabel($label);
        }

        return $this;
    }

    public function setFirstMaxLength(?int $length): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.attr.'.self::OPTION_MAX_LENGTH, $length);
        } else {
            $this->setMaxLength($length);
        }

        return $this;
    }

    public function setSecondMaxLength(?int $length): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.attr.'.self::OPTION_MAX_LENGTH, $length);
        } else {
            $this->setMaxLength($length);
        }

        return $this;
    }

    public function setFirstMinLength(?int $length): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.attr.'.self::OPTION_MIN_LENGTH, $length);
        } else {
            $this->setMaxLength($length);
        }

        return $this;
    }

    public function setSecondMinLength(?int $length): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.attr.'.self::OPTION_MIN_LENGTH, $length);
        } else {
            $this->setMaxLength($length);
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
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.attr.'.self::OPTION_PLACEHOLDER, $placeholder);
        } else {
            $this->setHtmlAttribute(self::OPTION_PLACEHOLDER, $placeholder);
        }

        return $this;
    }

    public function setSecondPlaceholder(?string $placeholder): self
    {
        if ($this->checkIsRepeated()) {
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.attr.'.self::OPTION_PLACEHOLDER, $placeholder);
        } else {
            $this->setHtmlAttribute(self::OPTION_PLACEHOLDER, $placeholder);
        }

        return $this;
    }

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

    /** RepeatedType keeps `attr` on the compound form, so plugin attrs go on both inputs. */
    private function setPasswordAttr(string $option, mixed $value): self
    {
        if (RepeatedType::class === $this->getAsDto()->getFormType()) {
            $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.attr.'.$option, $value);
            $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.attr.'.$option, $value);

            return $this;
        }

        $this->setHtmlAttribute($option, $value);

        return $this;
    }

    /** Move plugin attrs set before `isRepeated()` onto both inner inputs. */
    private function adoptPasswordAttrsForRepeated(): void
    {
        $attr = $this->getAsDto()->getFormTypeOption('attr');
        if (!\is_array($attr)) {
            return;
        }

        foreach ([self::OPTION_PLUGIN, self::OPTION_SWITCH, self::OPTION_GENERATOR] as $name) {
            if (\array_key_exists($name, $attr)) {
                $this->setPasswordAttr($name, $attr[$name]);
                unset($attr[$name]);
            }
        }

        $this->setFormTypeOption('attr', $attr);
    }
}
