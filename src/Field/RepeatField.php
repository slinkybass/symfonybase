<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field as EasyField;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RepeatField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private EasyField $innerField;

    public const OPTION_FIRST_OPTIONS = 'first_options';
    public const OPTION_SECOND_OPTIONS = 'second_options';

    public static function new(string $propertyName, $label = null): self
    {
        $field = new self();
        $field->innerField = EasyField::new($propertyName, $label);
        $field->initField($field->innerField);
        $field
            ->setFormType(RepeatedType::class);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
    }

    public function setType($type): self
    {
        $this->setFormTypeOption('type', $type);

        return $this;
    }

    public function setLabel(?string $label): self
    {
        $this->innerField->setLabel($label);
        $this->setFirstLabel($label);
        $this->setSecondLabel($label);

        return $this;
    }

    public function setFirstLabel(?string $label): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.label', $label);

        return $this;
    }

    public function setSecondLabel(?string $label): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.label', $label);

        return $this;
    }

    public function setPlaceholder(?string $placeholder): self
    {
        $this->setFirstPlaceholder($placeholder);
        $this->setSecondPlaceholder($placeholder);

        return $this;
    }

    public function setFirstPlaceholder(?string $placeholder): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.attr.' . self::OPTION_PLACEHOLDER, $placeholder);

        return $this;
    }

    public function setSecondPlaceholder(?string $placeholder): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.attr.' . self::OPTION_PLACEHOLDER, $placeholder);

        return $this;
    }

    public function setMaxLength(?int $maxLength): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.attr.' . self::OPTION_MAX_LENGTH, $maxLength);
        $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.attr.' . self::OPTION_MAX_LENGTH, $maxLength);

        return $this;
    }

    public function setMinLength(?int $minLength): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.attr.' . self::OPTION_MIN_LENGTH, $minLength);
        $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.attr.' . self::OPTION_MIN_LENGTH, $minLength);

        return $this;
    }
}
