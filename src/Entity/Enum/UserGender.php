<?php

namespace App\Entity\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Backed enum persisted on `User::gender` (integer column). Labels use `entities.user.fields.genders.*` translation keys.
 */
enum UserGender: int implements TranslatableInterface
{
    case male = 1;
    case female = 2;
    case nonbinary = 3;

    private const BASE_KEY = 'entities.user.fields.genders';

    /**
     * Key under `entities.user.fields.genders.<caseName>` for translators and `choices()`.
     */
    public function translationKey(): string
    {
        return self::BASE_KEY.'.'.$this->name;
    }

    /**
     * Returns the translated label for this enum case.
     *
     * @param string|null $locale the locale code (defaults to the current locale)
     */
    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->translationKey(), locale: $locale);
    }

    /**
     * @return array<string, int> map of `translationKey()` to backing value (for choice lists / APIs)
     */
    public static function choices(): array
    {
        return array_combine(
            array_map(fn (self $e) => $e->translationKey(), self::cases()),
            array_map(fn (self $e) => $e->value, self::cases())
        );
    }
}
