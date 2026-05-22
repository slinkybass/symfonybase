<?php

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Attribute\AsTwigFilter;
use Twig\Attribute\AsTwigFunction;

/**
 * Filters and helpers for enum cases in Twig (labels, choices, equality against name or value).
 */
class EnumExtension
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Returns the name of an enum case.
     */
    #[AsTwigFilter('enum_name')]
    public function enumName(\UnitEnum $enum): string
    {
        return $enum->name;
    }

    /**
     * Returns the backing value of a backed enum case, or null for pure enums.
     */
    #[AsTwigFilter('enum_value')]
    public function enumValue(\UnitEnum $enum): int|string|null
    {
        return $enum instanceof \BackedEnum ? $enum->value : null;
    }

    /**
     * Returns the translated label for an enum case.
     *
     * @param \UnitEnum   $enum   the enum case to display
     * @param string|null $locale the locale code (defaults to the current locale)
     */
    #[AsTwigFilter('enum_label')]
    public function enumLabel(\UnitEnum $enum, ?string $locale = null): string
    {
        if ($enum instanceof TranslatableInterface) {
            return $enum->trans($this->translator, $locale);
        }

        return $enum->name;
    }

    /**
     * Label-to-value map for form choices. Values are backing values; the enum must be backed.
     *
     * @param class-string<\BackedEnum> $enumClass
     * @param string|null               $locale    explicit locale or current request locale
     *
     * @return array<string, int|string>
     */
     #[AsTwigFilter('enum_choices')]
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
     * @param class-string<\BackedEnum> $enumClass
     */
    #[AsTwigFilter('enum_from_value')]
    public function enumFromValue(mixed $value, string $enumClass): ?\UnitEnum
    {
        return $enumClass::tryFrom($value);
    }

    /**
     * @param class-string<\UnitEnum> $enumClass
     */
    #[AsTwigFilter('enum_from_name')]
    public function enumFromName(string $name, string $enumClass): ?\UnitEnum
    {
        $cases = array_combine(
            array_map(fn (\UnitEnum $c) => $c->name, $enumClass::cases()),
            $enumClass::cases(),
        );

        return $cases[$name] ?? null;
    }

    /**
     * Returns whether the given enum case matches another case, a name or a value.
     *
     * @param \UnitEnum                 $enum  the enum case to compare
     * @param \UnitEnum|int|string|null $other the case, name or value to compare against
     */
    #[AsTwigFilter('enum_is')]
    public function enumIs(\UnitEnum $enum, \UnitEnum|int|string|null $other): bool
    {
        if ($other instanceof \UnitEnum) {
            return $enum === $other;
        }

        if ($enum instanceof \BackedEnum && $enum->value === $other) {
            return true;
        }

        return $enum->name === $other;
    }

    /**
     * @param class-string<\UnitEnum> $enumClass
     *
     * @return list<\UnitEnum>
     */
    #[AsTwigFunction('enum_cases')]
    public function enumCases(string $enumClass): array
    {
        return $enumClass::cases();
    }

    /**
     * @param class-string<\UnitEnum> $enumClass
     */
    #[AsTwigFunction('enum_count')]
    public function enumCount(string $enumClass): int
    {
        return count($enumClass::cases());
    }
}
