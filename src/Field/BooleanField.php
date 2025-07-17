<?php

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField as EasyField;

class BooleanField
{
    use FieldTrait;

	public const OPTION_CHECKED = 'checked';

    private EasyField $field;

    public static function new(string $propertyName, $label = null): self
    {
        $instance = new self();
        $instance->field = EasyField::new($propertyName, $label);

        $instance
            ->isSwitch(false)
            ->setDefaultColumns(12);

        return $instance;
    }

	public function isChecked(bool $checked = true): self
	{
		$this->setHtmlAttribute(self::OPTION_CHECKED, $checked);

		return $this;
	}

    public function isSwitch(bool $switch = true): self
    {
        $this->field->renderAsSwitch($switch);

        return $this;
    }

    public function hideTrue(bool $hide = true): self
    {
        $this->field->hideValueWhenTrue($hide);

        return $this;
    }

    public function hideFalse(bool $hide = true): self
    {
        $this->field->hideValueWhenFalse($hide);

        return $this;
    }
}
