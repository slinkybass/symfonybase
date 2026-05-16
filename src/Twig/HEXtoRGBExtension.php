<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * `#RRGGBB` to RGB channel triplets for templates (e.g. inline styles).
 */
class HEXtoRGBExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('hex_to_rgb', [$this, 'hexToRgb']),
        ];
    }

    /**
     * Parses `#RRGGBB` into `[R, G, B]` with integers in `0..255`.
     *
     * @return array{0: int, 1: int, 2: int}|null when the string does not match that shape
     */
    public function hexToRgb(string $hex): ?array
    {
        return sscanf($hex, '#%02x%02x%02x');
    }
}
