<?php

namespace App\Entity\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum UserGender: int implements TranslatableInterface
{
    case male = 1;
    case female = 2;
    case nobinary = 3;

    private const BASE_KEY = 'entities.user.fields.genders';

    public function translationKey(): string
    {
        return self::BASE_KEY . '.' . $this->name;
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->translationKey(), locale: $locale);
    }

    public static function choices(): array
    {
        return array_combine(
            array_map(fn(self $e) => $e->translationKey(), self::cases()),
            array_map(fn(self $e) => $e->value, self::cases())
        );
    }
}
