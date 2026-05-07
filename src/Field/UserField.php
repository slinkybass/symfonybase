<?php

namespace App\Field;

use App\Repository\Filter\User as UserFilter;
use App\Twig\Components\User as UserComponent;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;

class UserField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    private AssociationField $innerField;

    public const OPTION_VARIANT_DETAIL = 'variant_detail';
    public const OPTION_AVATAR_SIZE = 'avatar_size';
    public const OPTION_STACKED_AVATAR_SIZE = 'stacked_avatar_size';
    public const OPTION_ONLY_VERIFIED = 'only_verified';
    public const OPTION_ONLY_ACTIVE = 'only_active';
    public const OPTION_ASSOCIATION_TYPE = 'associationType';

    public const DEFAULT_VARIANT_DETAIL = UserComponent::VARIANT_CARD;
    public const DEFAULT_AVATAR_SIZE = UserComponent::DEFAULT_AVATAR_SIZE;
    public const DEFAULT_STACKED_AVATAR_SIZE = 'sm';
    public const DEFAULT_ONLY_VERIFIED = true;
    public const DEFAULT_ONLY_ACTIVE = true;

    public static function new(string $propertyName, $label = null): self
    {
        $field = new self();
        $field->innerField = AssociationField::new($propertyName, $label);
        $field->initField($field->innerField);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
        $this->setTemplatePath('field/user.html.twig');
        $this->setVariantDetail(self::DEFAULT_VARIANT_DETAIL);
        $this->setAvatarSize(self::DEFAULT_AVATAR_SIZE);
        $this->setStackedAvatarSize(self::DEFAULT_STACKED_AVATAR_SIZE);
        $this->onlyVerified(self::DEFAULT_ONLY_VERIFIED);
        $this->onlyActive(self::DEFAULT_ONLY_ACTIVE);
    }

    public function setQueryBuilder(\Closure $queryBuilderCallable): self
    {
        $this->innerField->setQueryBuilder($queryBuilderCallable);

        return $this;
    }

    public function setVariantDetail(string $variant): self
    {
        $this->setCustomOption(self::OPTION_VARIANT_DETAIL, $this->normalizeVariant($variant, self::DEFAULT_VARIANT_DETAIL));

        return $this;
    }

    public function setAvatarSize(string $size): self
    {
        $this->setCustomOption(self::OPTION_AVATAR_SIZE, $this->normalizeValue($size, self::DEFAULT_AVATAR_SIZE));

        return $this;
    }

    public function setStackedAvatarSize(string $size): self
    {
        $this->setCustomOption(self::OPTION_STACKED_AVATAR_SIZE, $this->normalizeValue($size, self::DEFAULT_STACKED_AVATAR_SIZE));

        return $this;
    }

    public function onlyVerified(bool $only = true): self
    {
        $this->setCustomOption(self::OPTION_ONLY_VERIFIED, $only);
        $this->applyUserQueryBuilder();

        return $this;
    }

    public function onlyActive(bool $only = true): self
    {
        $this->setCustomOption(self::OPTION_ONLY_ACTIVE, $only);
        $this->applyUserQueryBuilder();

        return $this;
    }

    private function normalizeVariant(string $value, string $fallback): string
    {
        $normalized = $this->normalizeValue($value, $fallback);

        if (\in_array($normalized, [UserComponent::VARIANT_CARD, UserComponent::VARIANT_BADGE], true)) {
            return $normalized;
        }

        return $fallback;
    }

    private function normalizeValue(string $value, string $fallback): string
    {
        $normalized = trim($value);

        return '' === $normalized ? $fallback : $normalized;
    }

    private function applyUserQueryBuilder(): void
    {
        $onlyVerified = (bool) $this->dto->getCustomOption(self::OPTION_ONLY_VERIFIED);
        $onlyActive = (bool) $this->dto->getCustomOption(self::OPTION_ONLY_ACTIVE);

        $this->innerField->setQueryBuilder(function ($er) use ($onlyVerified, $onlyActive) {
            $qb = $er->createQueryBuilder('u');

            if ($onlyVerified) {
                (new UserFilter\IsVerifiedFilter())->apply($qb);
            }

            if ($onlyActive) {
                (new UserFilter\IsActiveFilter())->apply($qb);
            }

            return $qb;
        });
    }
}
