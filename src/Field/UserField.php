<?php

namespace App\Field;

use App\Field\Trait\FieldTrait;
use App\Field\Trait\TomSelectFieldTrait;
use App\Repository\Filter\User as UserFilter;
use App\Twig\Components\User as UserComponent;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField as EasyAssociationField;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * User association picker built on `AssociationField`: filters (active/verified), card/badge display, and avatar sizing for Twig templates.
 */
class UserField implements FieldInterface
{
    use FieldTrait {
        applyDefaults as applyDefaultsTrait;
    }
    use TomSelectFieldTrait;

    private AssociationField $innerField;

    public const OPTION_VARIANT_DETAIL = 'variant_detail';
    public const OPTION_AVATAR_SIZE = 'avatar_size';
    public const OPTION_STACKED_AVATAR_SIZE = 'stacked_avatar_size';
    public const OPTION_SUBLABEL = 'sublabel';
    public const OPTION_ONLY_VERIFIED = 'only_verified';
    public const OPTION_ONLY_ACTIVE = 'only_active';
    public const OPTION_ASSOCIATION_TYPE = 'associationType';

    public const DEFAULT_VARIANT_DETAIL = UserComponent::VARIANT_CARD;
    public const DEFAULT_AVATAR_SIZE = UserComponent::DEFAULT_AVATAR_SIZE;
    public const DEFAULT_STACKED_AVATAR_SIZE = 'sm';
    public const DEFAULT_SUBLABEL = null;
    public const DEFAULT_ONLY_VERIFIED = true;
    public const DEFAULT_ONLY_ACTIVE = true;

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        $field = new self();
        $field->innerField = AssociationField::new($propertyName, $label);
        $field->initField($field->innerField);

        return $field;
    }

    private function applyDefaults(): void
    {
        $this->applyDefaultsTrait();
        // initField stamps App\AssociationField; AssociationConfigurator needs EasyAdmin's FQCN.
        $this->dto->setFieldFqcn(EasyAssociationField::class);
        $this->enablePlugin();
        $this->setTemplatePath('field/user.html.twig');
        $this->setVariantDetail(self::DEFAULT_VARIANT_DETAIL);
        $this->setAvatarSize(self::DEFAULT_AVATAR_SIZE);
        $this->setStackedAvatarSize(self::DEFAULT_STACKED_AVATAR_SIZE);
        $this->setSublabel(self::DEFAULT_SUBLABEL);
        $this->onlyVerified(self::DEFAULT_ONLY_VERIFIED);
        $this->onlyActive(self::DEFAULT_ONLY_ACTIVE);
    }

    /** CRUD controller of the user entity, used for links and autocomplete. */
    public function setCrudController(string $crudController): self
    {
        $this->innerField->setCrudController($crudController);

        return $this;
    }

    /**
     * Load users over AJAX instead of rendering every user.
     *
     * @param callable|null $callback Optional result mapper `(User $user): array`
     */
    public function autocomplete(bool $enable = true, ?callable $callback = null, ?string $template = null, bool $renderAsHtml = false): self
    {
        $this->innerField->autocomplete($enable, $callback, $template, $renderAsHtml);

        return $this;
    }

    /** Filter or sort the users. Replaces the active/verified filters from `onlyActive()` and `onlyVerified()`. */
    public function setQueryBuilder(\Closure $queryBuilderCallable): self
    {
        $this->innerField->setQueryBuilder($queryBuilderCallable);

        return $this;
    }

    /**
     * Detail layout. Pass `User::VARIANT_CARD` (default) or `User::VARIANT_BADGE`.
     *
     * @param UserComponent::VARIANT_* $variant
     */
    public function setVariantDetail(string $variant): self
    {
        $this->setCustomOption(self::OPTION_VARIANT_DETAIL, $this->normalizeVariant($variant, self::DEFAULT_VARIANT_DETAIL));

        return $this;
    }

    /** Avatar size on detail (`sm`, `md`, `lg`). Default `md`. */
    public function setAvatarSize(string $size): self
    {
        $this->setCustomOption(self::OPTION_AVATAR_SIZE, $this->normalizeValue($size, self::DEFAULT_AVATAR_SIZE));

        return $this;
    }

    /** Avatar size when several users are stacked. Default `sm`. */
    public function setStackedAvatarSize(string $size): self
    {
        $this->setCustomOption(self::OPTION_STACKED_AVATAR_SIZE, $this->normalizeValue($size, self::DEFAULT_STACKED_AVATAR_SIZE));

        return $this;
    }

    /** Secondary line under the user name. `null` hides it. */
    public function setSublabel(?string $sublabel): self
    {
        $this->setCustomOption(self::OPTION_SUBLABEL, $sublabel);

        return $this;
    }

    /** Limit the picker to verified users. Default on. */
    public function onlyVerified(bool $only = true): self
    {
        $this->setCustomOption(self::OPTION_ONLY_VERIFIED, $only);
        $this->applyUserQueryBuilder();

        return $this;
    }

    /** Limit the picker to active users. Default on. */
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

        // AssociationConfigurator passes an existing QueryBuilder (alias `entity`), not the repository.
        $this->innerField->setQueryBuilder(function ($qb) use ($onlyVerified, $onlyActive) {
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
