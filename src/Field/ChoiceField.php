<?php

namespace App\Field;

use App\Field\Trait\ChoiceControlTrait;
use App\Field\Trait\FieldTrait;
use App\Field\Trait\TomSelectFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `ChoiceField` wrapper with TomSelect options (`TomSelectFieldTrait`). */
class ChoiceField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use TomSelectFieldTrait;
    use ChoiceControlTrait;

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
    }

    /**
     * Options as `[label => value]`, or a callable that returns that array.
     *
     * @param array<mixed>|callable $choices
     */
    public function setChoices($choices): self
    {
        $this->innerField->setChoices($choices);

        return $this;
    }

    /**
     * Options whose labels are translation keys or `TranslatableInterface` objects.
     *
     * @param array<string|TranslatableInterface>|callable $choices
     */
    public function setTransChoices($choices): self
    {
        $this->innerField->setTranslatableChoices($choices);

        return $this;
    }
}
