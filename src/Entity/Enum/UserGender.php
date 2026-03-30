<?php

namespace App\Entity\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum UserGender: int implements TranslatableInterface
{
    case male = 1;
    case female = 2;
    case nonbinary = 3;

    private const BASE_KEY = 'entities.user.fields.genders';

    /**
     * Returns the translation key for this enum case.
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
     * Returns all cases as a translation-key indexed array of values, suitable for form choices.
     *
     * @return array<string, int>
     */
    public static function choices(): array
    {
        return array_combine(
            array_map(fn (self $e) => $e->translationKey(), self::cases()),
            array_map(fn (self $e) => $e->value, self::cases())
        );
    }
}
