<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension to decode JSON strings.
 */
class JsonDecodeExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            'json_decode' => new TwigFilter('json_decode', [$this, 'getJsonDecode']),
        ];
    }

    /**
     * Returns the decoded JSON string.
     *
     * @param string $string The string to decode
     *
     * @return mixed The decoded string
     */
    public function getJsonDecode($string)
    {
        return json_decode($string);
    }
}
