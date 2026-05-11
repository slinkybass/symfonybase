<?php

namespace App\Twig\Components;

use App\Entity\User as UserEntity;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class User
{
    public const VARIANT_CARD = 'card';
    public const VARIANT_BADGE = 'badge';
    public const DEFAULT_AVATAR_SIZE = 'md';
    public const DEFAULT_SHOW_BADGE_LABEL = true;

    public ?UserEntity $user = null;
    public string $variant = self::VARIANT_CARD;
    public string $avatarSize = self::DEFAULT_AVATAR_SIZE;
    public ?string $url = null;
    public ?string $sublabel = null;
    public bool $showBadgeLabel = self::DEFAULT_SHOW_BADGE_LABEL;

    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getLabel(): string
    {
        return $this->user ? $this->user->getFullName() : $this->translator->trans('user.anonymous', [], 'EasyAdminBundle');
    }
}
