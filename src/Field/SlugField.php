<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\SlugifyFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `SlugField` wrapper with slugify plugin options. */
class SlugField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use SlugifyFieldTrait;

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


    /** Source field name(s) whose values feed the slug (EasyAdmin `target` form option). */
    public function setTarget(string|array $target): self
    {
        $this->innerField->setTargetFieldName($target);

        return $this;
    }
}
