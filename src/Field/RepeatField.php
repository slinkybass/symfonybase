<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field as EasyField;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Contracts\Translation\TranslatableInterface;

/** Symfony `RepeatedType` exposed as an EasyAdmin field with paired first/second option helpers. */
class RepeatField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const OPTION_FIRST_OPTIONS = 'first_options';
    public const OPTION_SECOND_OPTIONS = 'second_options';

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
        $this->setFormType(RepeatedType::class);
    }

    /**
     * Symfony form type rendered in each of the two inputs (e.g. `PasswordType::class`).
     *
     * @param class-string $type
     */
    public function setType(string $type): self
    {
        $this->setFormTypeOption('type', $type);

        return $this;
    }

    /** Label of the field and of both inputs. */
    public function setLabel(?string $label): self
    {
        $this->innerField->setLabel($label);
        $this->setFirstLabel($label);
        $this->setSecondLabel($label);

        return $this;
    }

    /** Label of the first input. */
    public function setFirstLabel(?string $label): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.label', $label);

        return $this;
    }

    /** Label of the confirmation input. */
    public function setSecondLabel(?string $label): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.label', $label);

        return $this;
    }

    /** Placeholder on both inputs. */
    public function setPlaceholder(?string $placeholder): self
    {
        $this->setFirstPlaceholder($placeholder);
        $this->setSecondPlaceholder($placeholder);

        return $this;
    }

    /** Placeholder of the first input. */
    public function setFirstPlaceholder(?string $placeholder): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.attr.'.self::OPTION_PLACEHOLDER, $placeholder);

        return $this;
    }

    /** Placeholder of the confirmation input. */
    public function setSecondPlaceholder(?string $placeholder): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.attr.'.self::OPTION_PLACEHOLDER, $placeholder);

        return $this;
    }

    /** HTML `maxlength` on both inputs. */
    public function setMaxLength(?int $maxLength): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.attr.'.self::OPTION_MAX_LENGTH, $maxLength);
        $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.attr.'.self::OPTION_MAX_LENGTH, $maxLength);

        return $this;
    }

    /** HTML `minlength` on both inputs. */
    public function setMinLength(?int $minLength): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS.'.attr.'.self::OPTION_MIN_LENGTH, $minLength);
        $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS.'.attr.'.self::OPTION_MIN_LENGTH, $minLength);

        return $this;
    }
}
