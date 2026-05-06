<?php

namespace App\Twig\Components;

use App\Entity\User as UserEntity;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class User
{
    public const VARIANT_CARD = 'card';
    public const VARIANT_INLINE = 'inline';
    public ?UserEntity $user = null;
    public string $variant = self::VARIANT_CARD;
    public string $avatarSize = 'md';
    public ?string $url = null;
    public ?string $sublabel = null;

    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function getLabel(): string
    {
        return $this->user ? $this->user->getFullName() : $this->translator->trans('user.anonymous', [], 'EasyAdminBundle');
    }

    public function getSublabel(): string
    {
        return $this->sublabel !== null ? $this->sublabel : ($this->user ? $this->user->getRole() : '');
    }
}
