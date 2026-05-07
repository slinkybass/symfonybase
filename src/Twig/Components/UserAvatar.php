<?php

namespace App\Twig\Components;

use App\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class UserAvatar
{
    public ?User $user = null;
    public string $size = 'md';
    public string $class = '';
    public bool $zoom = false;

    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function getClasses(): string
    {
        $sizeClass = $this->size ? "avatar-{$this->size}" : '';

        return trim("avatar $sizeClass {$this->class}");
    }

    public function getLabel(): string
    {
        return $this->user ? $this->user->getFullName() : $this->translator->trans('user.anonymous', [], 'EasyAdminBundle');
    }

    public function getInitial(): string
    {
        return mb_strtoupper(mb_substr($this->getLabel(), 0, 1));
    }

    public function getAvatar(): ?string
    {
        $avatar = $this->user?->getAvatar();

        return $avatar !== null && $avatar !== '' ? $avatar : null;
    }
}
