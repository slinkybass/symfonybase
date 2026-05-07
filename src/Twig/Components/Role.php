<?php

namespace App\Twig\Components;

use App\Entity\Role as RoleEntity;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Role
{
    public RoleEntity $role;

    public function getColor(): string
    {
        $seed = $this->role->getName();

        return sprintf('hsl(%d, 65%%, 42%%)', $this->hueFromSeed($seed));
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
