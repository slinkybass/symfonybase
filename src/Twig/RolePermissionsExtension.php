<?php

namespace App\Twig;

use App\Entity\User;
use App\Service\RolePermissions;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Attribute\AsTwigFunction;

/**
 * Twig helpers that delegate to `RolePermissions` for the current (or given) `User`.
 *
 * Non-`User` security tokens are treated as having no permissions.
 */
class RolePermissionsExtension 
{
    public function __construct(
        private readonly RolePermissions $rolePermissions,
        private readonly Security $security,
    ) {
    }

    /**
     * Checks whether a user has a specific permission.
     *
     * @param string    $perm the permission identifier to check
     * @param User|null $user the user to check (defaults to the current user)
     */
    #[AsTwigFunction('has_permission')]
    public function hasPermission(string $perm, ?User $user = null): bool
    {
        $user = $user ?? $this->security->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $this->rolePermissions->userHasPermission($user, $perm);
    }

    /**
     * Checks whether a user has access to a specific CRUD.
     *
     * @param string    $crud the CRUD name to check
     * @param User|null $user the user to check (defaults to the current user)
     */
    #[AsTwigFunction('has_permission_crud')]
    public function hasPermissionCrud(string $crud, ?User $user = null): bool
    {
        $user = $user ?? $this->security->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $this->rolePermissions->userHasPermissionCrud($user, $crud);
    }

    /**
     * Checks whether a user has permission to perform a specific action on a CRUD.
     *
     * @param string    $crud   the CRUD name to check
     * @param string    $action the action to check (e.g. 'index', 'new', 'edit', 'delete')
     * @param User|null $user   the user to check (defaults to the current user)
     */
    #[AsTwigFunction('has_permission_crud_action')]
    public function hasPermissionCrudAction(string $crud, string $action, ?User $user = null): bool
    {
        $user = $user ?? $this->security->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $this->rolePermissions->userHasPermissionCrudAction($user, $crud, $action);
    }
}
