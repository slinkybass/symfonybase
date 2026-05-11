<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension to decode JSON strings.
 */
class JsonDecodeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('json_decode', [$this, 'jsonDecode']),
        ];
    }

    /**
     * Decodes a JSON string.
     */
    public function jsonDecode(string $string): mixed
    {
        return json_decode($string);
    }
}
