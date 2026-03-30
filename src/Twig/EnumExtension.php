<?php

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension to display and resolve PHP enums in Twig templates.
 */
class EnumExtension extends AbstractExtension
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('enum_label', [$this, 'enumLabel']),
            new TwigFilter('enum_choices', [$this, 'enumChoices']),
            new TwigFilter('enum_from_value', [$this, 'enumFromValue']),
            new TwigFilter('enum_from_name', [$this, 'enumFromName']),
        ];
    }

    /**
     * Returns the translated label for an enum case.
     *
     * @param \UnitEnum   $enum   the enum case to display
     * @param string|null $locale the locale code (defaults to the current locale)
     */
    public function enumLabel(\UnitEnum $enum, ?string $locale = null): string
    {
        if ($enum instanceof TranslatableInterface) {
            return $enum->trans($this->translator, $locale);
        }

        return $enum->name;
    }

    /**
     * Returns all cases of an enum class as a label-value array.
     *
     * @param class-string $enumClass the fully qualified enum class name
     * @param string|null  $locale    the locale code (defaults to the current locale)
     *
     * @return array<string, mixed>
     */
    public function enumChoices(string $enumClass, ?string $locale = null): array
    {
        $choices = [];
        foreach ($enumClass::cases() as $case) {
            $label = $case instanceof TranslatableInterface ? $case->trans($this->translator, $locale) : $case->name;
            $choices[$label] = $case->value;
        }

        return $choices;
    }

    /**
     * Returns the enum case matching the given value, or null if not found.
     *
     * @param mixed        $value     the value to match against enum cases
     * @param class-string $enumClass the fully qualified enum class name
     */
    public function enumFromValue(mixed $value, string $enumClass): ?\UnitEnum
    {
        foreach ($enumClass::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Returns the enum case matching the given name, or null if not found.
     *
     * @param string       $name      the name to match against enum cases
     * @param class-string $enumClass the fully qualified enum class name
     */
    public function enumFromName(string $name, string $enumClass): ?\UnitEnum
    {
        foreach ($enumClass::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }
}
