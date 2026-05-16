<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Decodes JSON strings in Twig via the `json_decode` filter (default `json_decode()` semantics).
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
     * @return mixed|null `null` on invalid JSON or JSON `null`
     */
    public function jsonDecode(string $string): mixed
    {
        return json_decode($string);
    }
}
