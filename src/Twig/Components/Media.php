<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Media
{
    public const DEFAULT_SIZE = 'md';

    private const IMAGE_EXTENSIONS = ['apng', 'avif', 'bmp', 'gif', 'jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp', 'png', 'svg', 'webp'];

    public string $src;
    public string $size = self::DEFAULT_SIZE;
    public string $class = '';
    public bool $zoom = false;

    public function getClasses(): string
    {
        $sizeClass = 'avatar-'.($this->size ? $this->size : self::DEFAULT_SIZE);

        return trim("avatar $sizeClass {$this->class}");
    }

    public function getFileExtension(): string
    {
        $path = $this->getPathFromSrc();
        $parts = explode('.', $path);

        return strtolower(end($parts) ?: '');
    }

    public function getDisplayName(): string
    {
        $basename = basename($this->getPathFromSrc());

        return pathinfo($basename, PATHINFO_FILENAME) ?: '';
    }

    public function isImage(): bool
    {
        if (str_starts_with($this->src, 'data:image')) {
            return true;
        }

        return in_array($this->getFileExtension(), self::IMAGE_EXTENSIONS, true);
    }

    private function getPathFromSrc(): string
    {
        return parse_url($this->src, PHP_URL_PATH) ?: $this->src;
    }
}
