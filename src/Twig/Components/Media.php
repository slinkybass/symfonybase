<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Media
{
    private const IMAGE_EXTENSIONS = ['apng', 'avif', 'bmp', 'gif', 'jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp', 'png', 'svg', 'webp'];

    public string $src;
    public string $size = 'md';
    public string $class = '';
    public bool $zoom = false;

    public function getClasses(): string
    {
        $sizeClass = $this->size ? "avatar-{$this->size}" : '';

        return trim("avatar $sizeClass {$this->class}");
    }

    public function getFileExtension(): string
    {
        $path = explode('?', $this->src, 2)[0];
        $parts = explode('.', $path);

        return strtolower(end($parts) ?: '');
    }

    public function getDisplayName(): string
    {
        $beforeFirstDot = explode('.', $this->src, 2)[0];
        $normalized = str_replace('\\', '/', $beforeFirstDot);
        $parts = explode('/', $normalized);

        return end($parts) ?: '';
    }

    public function isImage(): bool
    {
        if (str_starts_with($this->src, 'data:image')) {
            return true;
        }

        return in_array($this->getFileExtension(), self::IMAGE_EXTENSIONS, true);
    }
}
