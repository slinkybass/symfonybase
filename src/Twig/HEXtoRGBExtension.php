<?php

namespace App\Twig;

use Twig\Attribute\AsTwigFilter;

/**
 * `#RRGGBB` to RGB channel triplets for templates (e.g. inline styles).
 */
class HEXtoRGBExtension
{
    /**
     * Parses `#RRGGBB` into `[R, G, B]` with integers in `0..255`.
     *
     * @return array{0: int, 1: int, 2: int}|null when the string does not match that shape
     */
    #[AsTwigFilter('hex_to_rgb')]
    public function hexToRgb(string $hex): ?array
    {
        return sscanf($hex, '#%02x%02x%02x');
    }
}
