<?php

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension to display enums.
 */
class EnumExtension extends AbstractExtension
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function getFilters()
    {
        return [
            new TwigFilter('enum_label', [$this, 'enumLabel']),
            new TwigFilter('enum_choices', [$this, 'enumChoices']),
            new TwigFilter('enum_from_value', [$this, 'enumFromValue']),
            new TwigFilter('enum_from_name', [$this, 'enumFromName']),
        ];
    }

    /**
     * Displays the label corresponding to the enum.
     *
     * @param mixed $enum The enum to display
     * @param string $locale The locale code
     *
     * @return string The label corresponding to $value
     */
    public function enumLabel($enum, ?string $locale = null)
    {
        if ($enum instanceof TranslatableInterface) {
            return $enum->trans($this->translator, $locale);
        }
        return $enum->name;
    }

    /**
     * Returns the enum choices.
     *
     * @param string $enumClass Enum class name
     * @param string $locale The locale code
     * 
     * @return array<string, mixed>
     */
    public function enumChoices(string $enumClass, ?string $locale = null): array
    {
        $choices = [];
        foreach ($enumClass::cases() as $case) {
            if ($case instanceof TranslatableInterface) {
                $label = $case->trans($this->translator, $locale);
            } else {
                $label = $case->name;
            }
            $choices[$label] = $case->value;
        }
        return $choices;
    }

    /**
     * Returns the enum from its value.
     * 
     * @param mixed $value Enum 'value' property
     * @param string $enumClass Enum class name
     * 
     * @return object
     */
    public function enumFromValue($value, string $enumClass): ?object
    {
        foreach ($enumClass::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Returns the enum from its name.
     * 
     * @param string $name Enum 'name' property
     * @param string $enumClass Enum class name
     * 
     * @return object
     */
    public function enumFromName(string $name, string $enumClass): ?object
    {
        foreach ($enumClass::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }
}
