<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension to convert HEX color codes to RGB values.
 */
class HEXtoRGBExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('hex_to_rgb', [$this, 'HEXtoRGB']),
        ];
    }

    /**
     * Returns the array of RGB values.
     *
     * @param string $hex The HEX color code
     *
     * @return array The array of RGB values
     */
    public function HEXtoRGB($hex)
    {
        return sscanf($hex, "#%02x%02x%02x");
    }
}
