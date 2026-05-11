<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension to convert HEX color codes to RGB values.
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
     * Converts a HEX color code to an array of RGB values.
     *
     * @param string $hex the HEX color code (e.g. '#ff0000')
     *
     * @return array<int, int>|null
     */
    public function hexToRgb(string $hex): ?array
    {
        return sscanf($hex, '#%02x%02x%02x');
    }
}
