<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\TextareaFieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField as EasyField;
use Symfony\Contracts\Translation\TranslatableInterface;

/** EasyAdmin `TextareaField` wrapper with autogrow plugin options. */
class TextareaField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use TextareaFieldTrait;

    private EasyField $innerField;

    public const DEFAULT_ROWS = 5;

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
        $this->setRows(self::DEFAULT_ROWS);
        $this->setMaxHeight(self::DEFAULT_MAX_HEIGHT);
    }

    /** Visible rows (EasyAdmin `setNumOfRows`). */
    public function setRows(int $rows): self
    {
        $this->innerField->setNumOfRows($rows);

        return $this;
    }
}
