<?php

namespace App\Field\Trait;

use App\Field\RepeatField;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 * Password visibility / generator options for `PasswordField` (`data-password-*`).
 *
 * Plugin attrs go through {@see setPasswordAttr()} so RepeatedType keeps them on both inputs.
 */
trait PasswordFieldTrait
{
    public const OPTION_PLUGIN = 'data-password-field';
    public const OPTION_SWITCH = 'data-password-switch';
    public const OPTION_GENERATOR = 'data-password-generator';

    /** Load the password plugin (`data-password-field`). `false` keeps a plain password input. */
    public function enablePlugin(bool $enable = true): self
    {
        // Flag via setPasswordAttr so RepeatedType gets both inputs; assets only here.
        $this->enableMappedPlugin('form-type-password', null, $enable);

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
