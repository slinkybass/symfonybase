<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Dto\AssetsDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field as EasyField;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RepeatField
{
    use FieldTrait;

    public const OPTION_FIRST_OPTIONS = 'first_options';
    public const OPTION_SECOND_OPTIONS = 'second_options';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);
        $instance->field->getAsDto()->setAssets(new AssetsDto());

        $instance
            ->setFormType(RepeatedType::class)
            ->setDefaultColumns(12);

        return $instance;
    }

    public function setType($type): self
    {
        $this->setFormTypeOption('type', $type);

        return $this;
    }

    public function setLabel(?string $label): self
    {
        $this->setFirstLabel($label);
        $this->setSecondLabel($label);

        return $this;
    }

    public function setFirstLabel(?string $val): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.label', $val);

        return $this;
    }

    public function setSecondLabel(?string $val): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.label', $val);

        return $this;
    }

    public function setPlaceholder(?string $val): self
    {
        $this->setFirstPlaceholder($val);
        $this->setSecondPlaceholder($val);

        return $this;
    }

    public function setFirstPlaceholder(?string $val): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.attr.' . self::OPTION_PLACEHOLDER, $val);

        return $this;
    }

    public function setSecondPlaceholder(?string $val): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.attr.' . self::OPTION_PLACEHOLDER, $val);

        return $this;
    }

    public function setMaxLength(?int $val): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.attr.' . self::OPTION_MAX_LENGTH, $val);
        $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.attr.' . self::OPTION_MAX_LENGTH, $val);

        return $this;
    }

    public function setMinLength(?int $val): self
    {
        $this->setFormTypeOption(RepeatField::OPTION_FIRST_OPTIONS . '.attr.' . self::OPTION_MIN_LENGTH, $val);
        $this->setFormTypeOption(RepeatField::OPTION_SECOND_OPTIONS . '.attr.' . self::OPTION_MIN_LENGTH, $val);

        return $this;
    }
}
