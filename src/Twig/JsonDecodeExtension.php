<?php

namespace App\Twig;

use Twig\Attribute\AsTwigFilter;

/**
 * Decodes JSON strings in Twig via the `json_decode` filter (default `json_decode()` semantics).
 */
class JsonDecodeExtension
{
    /**
     * @return mixed|null `null` on invalid JSON or JSON `null`
     */
    #[AsTwigFilter('json_decode')]
    public function jsonDecode(string $string): mixed
    {
        return json_decode($string);
    }
}
