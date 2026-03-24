<?php

namespace App\Twig;

use App\Entity\User;
use App\Service\RolePermissions;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension to check user permissions in Twig templates.
 */
class RolePermissionsExtension extends AbstractExtension
{
    public function __construct(private RolePermissions $rolePermissions, private Security $security)
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('has_permission', [$this, 'hasPermission']),
            new TwigFunction('has_permission_crud', [$this, 'hasPermissionCrud']),
            new TwigFunction('has_permission_crud_action', [$this, 'hasPermissionCrudAction']),
        ];
    }

    /**
     * Checks whether a user has a specific permission.
     *
     * @param string $permission  the permission to check
     * @param User|null $user     the user to check (defaults to the current user)
     *
     * @return bool returns true if the user has the permission, false otherwise
     */
    public function hasPermission(string $perm, ?User $user = null): bool
    {
        $user = $user ?? $this->security->getUser();
        if (!$user instanceof User) return false;
        return $this->rolePermissions->userHasPermission($user, $perm);
    }

    /**
     * Checks whether a user has a specific CRUD permission.
     *
     * @param string $crud        the CRUD name to check
     * @param User|null $user     the user to check (defaults to the current user)
     *
     * @return bool returns true if the user has the CRUD permission, false otherwise
     */
    public function hasPermissionCrud(string $crud, ?User $user = null): bool
    {
        $user = $user ?? $this->security->getUser();
        if (!$user instanceof User) return false;
        return $this->rolePermissions->userHasPermissionCrud($user, $crud);
    }

    /**
     * Checks whether a user has a specific CRUD permission.
     *
     * @param string $crud        the CRUD name to check
     * @param string $action      the CRUD action to check
     * @param User|null $user     the user to check (defaults to the current user)
     *
     * @return bool returns true if the user has the CRUD permission, false otherwise
     */
    public function hasPermissionCrudAction(string $crud, string $action, ?User $user = null): bool
    {
        $user = $user ?? $this->security->getUser();
        if (!$user instanceof User) return false;
        return $this->rolePermissions->userHasPermissionCrudAction($user, $crud, $action);
    }
}
