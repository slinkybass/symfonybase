<?php

namespace App\Entity\Enums;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum UserGender: int implements TranslatableInterface
{
    case male = 1;
    case female = 2;
    case nobinary = 3;

    private static function getTranslateChain(): string
    {
        return 'entities.user.fields.genders.%s';
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans(sprintf(self::getTranslateChain(), $this->name), locale: $locale);
    }

    public static function getChoices(): array
    {
        return array_reduce(self::cases(), function ($o, $e) {
            $o[sprintf(self::getTranslateChain(), $e->name)] = $e->value;
            return $o;
        }, []);
    }
}