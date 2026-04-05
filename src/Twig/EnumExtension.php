<?php

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig extension to display, resolve and introspect PHP enums in Twig templates.
 */
class EnumExtension extends AbstractExtension
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('enum_name', [$this, 'enumName']),
            new TwigFilter('enum_value', [$this, 'enumValue']),
            new TwigFilter('enum_label', [$this, 'enumLabel']),
            new TwigFilter('enum_choices', [$this, 'enumChoices']),
            new TwigFilter('enum_from_value', [$this, 'enumFromValue']),
            new TwigFilter('enum_from_name', [$this, 'enumFromName']),
            new TwigFilter('enum_is', [$this, 'enumIs']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('enum_cases', [$this, 'enumCases']),
            new TwigFunction('enum_count', [$this, 'enumCount']),
        ];
    }

    /**
     * Returns the name of an enum case.
     */
    public function enumName(\UnitEnum $enum): string
    {
        return $enum->name;
    }

    /**
     * Returns the backing value of a backed enum case, or null for pure enums.
     */
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
    public function enumLabel(\UnitEnum $enum, ?string $locale = null): string
    {
        if ($enum instanceof TranslatableInterface) {
            return $enum->trans($this->translator, $locale);
        }

        return $enum->name;
    }

    /**
     * Returns all cases of an enum class as a label-value array, suitable for form choices.
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
        return $enumClass::tryFrom($value);
    }

    /**
     * Returns the enum case matching the given name, or null if not found.
     *
     * @param string       $name      the name to match against enum cases
     * @param class-string $enumClass the fully qualified enum class name
     */
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
     * Returns all cases of an enum class as an array of enum instances.
     *
     * @param class-string $enumClass the fully qualified enum class name
     *
     * @return \UnitEnum[]
     */
    public function enumCases(string $enumClass): array
    {
        return $enumClass::cases();
    }

    /**
     * Returns the number of cases in an enum class.
     *
     * @param class-string $enumClass the fully qualified enum class name
     */
    public function enumCount(string $enumClass): int
    {
        return count($enumClass::cases());
    }
}
