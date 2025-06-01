<?php

namespace App\Twig;

use Symfony\Component\Intl\Locales;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension to provide locale-related filters.
 */
class LocaleExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('locale_name', [$this, 'localeName']),
        ];
    }

    /**
     * Returns the localized name of a given locale code, capitalized.
     *
     * @param string $locale The locale code (e.g., 'en', 'fr', 'es')
     *
     * @return string The localized and capitalized name of the locale
     */
    public function localeName(string $locale): string
    {
        return ucfirst(Locales::getName($locale));
    }
}
