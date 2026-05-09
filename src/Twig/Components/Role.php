<?php

namespace App\Twig\Components;

use App\Entity\Role as RoleEntity;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Role
{
    public RoleEntity $role;
    public string $size = 'md';
    public string $class = '';
    public ?string $url = null;

    public function getClasses(): string
    {
        $sizeClass = $this->size ? "badge-{$this->size}" : '';

        return trim("badge $sizeClass {$this->class}");
    }

    public function getBackgroundColor(): string
    {
        return sprintf('hsl(%d, 32%%, 90%%)', $this->hue());
    }

    public function getForegroundColor(): string
    {
        return sprintf('hsl(%d, 40%%, 45%%)', $this->hue());
    }

    private function hue(): int
    {
        return $this->hueFromSeed((string) $this->role->getId().$this->role->getName());
    }

    private function hueFromSeed(string $seed): int
    {
        $hash = 0;
        foreach (str_split($seed) as $char) {
            $hash = (($hash << 5) - $hash) + ord($char);
            $hash &= 0xFFFFFFFF;
        }

        return abs($hash) % 360;
    }
}
