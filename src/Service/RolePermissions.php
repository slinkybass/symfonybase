<?php

namespace App\Service;

use App\Entity\Role;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Service responsible for managing role-based permissions within the application,
 * particularly those associated with EasyAdmin CRUD controllers.
 *
 * It dynamically detects available permissions by scanning controller files,
 * compares role hierarchies, and groups permissions in a structured way.
 */
class RolePermissions
{
    private KernelInterface $kernel;

    public const CRUD_PREFIX = 'crud';
    private const CRUD_PATHS = [
        '/src/Controller/Admin/Cruds'
    ];

    /**
     * @const array
     * List of CRUD-related entities and actions permissions that should be excluded from permissions.
     */
    private const DISABLED_CRUD_PERMISSIONS = [
        'config_new',
        'config_edit',
        'config_detail',
        'config_delete',
        'settings_new',
        'settings_edit',
        'settings_detail',
        'settings_delete',
    ];

    /**
     * @const array
     * Additional CRUD-related actions permissions not derived from controllers.
     */
    private const EXTRA_CRUD_ACTION_PERMISSIONS = [
        'admin_impersonate',
        'user_impersonate',
    ];

    /**
     * @const array
     * Additional permissions.
     */
    private const EXTRA_PERMISSIONS = [
        'media',
        'media_tree',
        'media_upload',
        'media_edit',
        'media_folders',
    ];

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Checks whether a role is higher or equal in permission level compared to another role.
     *
     * @param Role $role          the base role to compare
     * @param Role $roleToCompare the role to compare against
     *
     * @return bool|null returns true if $role is equal or higher, false if not, or null if undetermined
     */
    public function isUp(Role $role, Role $roleToCompare): ?bool
    {
        if ($role == $roleToCompare) return true;
        if (!count($role->getPermissions())) return false;
        foreach ($role->getPermissions() as $name => $value) {
            if (!$value && $this->roleHasPermission($roleToCompare, $name)) return false;
        }
        return true;
    }

    /**
     * Checks whether a role has a specific permission.
     *
     * @param Role $role          the role to check
     * @param string $permission  the permission to check
     *
     * @return bool returns true if the role has the permission, false otherwise
     */
    public function roleHasPermission(Role $role, string $permission): ?bool
    {
        $permission = $this->formatPermission($permission);
        return $role->getPermissions()[$permission] ?? false;
    }

    /**
     * Checks whether a user has a specific permission.
     *
     * @param User $user          the user to check
     * @param string $permission  the permission to check
     *
     * @return bool returns true if the user has the permission, false otherwise
     */
    public function userHasPermission(User $user, string $permission): ?bool
    {
        return $this->roleHasPermission($user->getRole(), $permission);
    }

    /**
     * Checks whether a user has a specific CRUD permission.
     *
     * @param User $user          the user to check
     * @param string $crud        the CRUD name to check
     *
     * @return bool returns true if the user has the CRUD permission, false otherwise
     */
    public function userHasPermissionCrud(User $user, string $crud): ?bool
    {
        return $this->userHasPermission($user, self::CRUD_PREFIX . '_' . $crud);
    }

    /**
     * Checks whether a user has a specific CRUD permission.
     *
     * @param User $user          the user to check
     * @param string $crud        the CRUD name to check
     * @param string $action      the CRUD action to check
     *
     * @return bool returns true if the user has the CRUD permission, false otherwise
     */
    public function userHasPermissionCrudAction(User $user, string $crud, string $action): ?bool
    {
        return $this->userHasPermission($user, self::CRUD_PREFIX . '_' . $crud . '_' . $action);
    }

    /**
     * Recursively traverses a multi-level permissions array and applies a callback to each permission.
     *
     * @param array       $permissions a nested array of permissions in a tree-like structure
     * @param callable    $callback    A function to be called for each permission. It receives the permission and its parent.
     * @param string|null $parent      the parent permission of the current level (null for root)
     * @param int         $level       the current level in the tree
     *
     * @return void
     */
    public function loopPermissions(array $permissions, callable $callback, ?string $parent = null, int $level = 0)
    {
        foreach ($permissions as $permission => $childrenPermissions) {
            $callback($permission, $parent, $level);
            if (is_array($childrenPermissions)) {
                $this->loopPermissions($childrenPermissions, $callback, $permission, $level + 1);
            }
        }
    }

    /**
     * Generates a flat list of available CRUD-related permissions
     * by scanning controller files and applying naming conventions.
     *
     * @return array a flat array representing permissions
     */
    private function getCrudPermissions(): array
    {
        $finder = new Finder();
        $perms = [];
        foreach (self::CRUD_PATHS as $crudsPath) {
            $crudsPath = $this->kernel->getProjectDir() . rtrim($crudsPath, '/');
            if (!file_exists($crudsPath)) continue;
            $finder->files()->in($crudsPath)->name('*.php');
            foreach ($finder as $file) {
                $fileName = $file->getFilename();
                if (!str_ends_with($fileName, 'CrudController.php')) continue;
                $crudName = $this->formatPermission(str_replace('CrudController.php', '', $fileName));
                if (in_array($crudName, $this->getDisabledCrudPermissions())) continue;
                $perms[] = self::CRUD_PREFIX . '_' . $crudName;
                $actionNames = [Action::NEW, Action::DETAIL, Action::EDIT, Action::DELETE];
                foreach ($actionNames as $actionName) {
                    $actionName = $this->formatPermission($actionName);
                    if (in_array($crudName . '_' . $actionName, $this->getDisabledCrudPermissions())) continue;
                    $perms[] = self::CRUD_PREFIX . '_' . $crudName . '_' . $actionName;
                }
            }
            foreach ($this->getExtraCrudActionPermissions() as $actionName) {
                $perms[] = self::CRUD_PREFIX . '_' . $actionName;
            }
        }
        return $perms;
    }

    /**
     * Generates a flat list of available disabled CRUD permissions.
     *
     * @return array a flat array representing disabled CRUD permissions
     */
    private function getDisabledCrudPermissions(): array
    {
        return array_map(fn($perm) => $this->formatPermission($perm), self::DISABLED_CRUD_PERMISSIONS);
    }

    /**
     * Generates a flat list of available extra CRUD action permissions.
     *
     * @return array a flat array representing extra CRUD action permissions
     */
    private function getExtraCrudActionPermissions(): array
    {
        return array_map(fn($perm) => $this->formatPermission($perm), self::EXTRA_CRUD_ACTION_PERMISSIONS);
    }

    /**
     * Generates a flat list of available extra permissions.
     *
     * @return array a flat array representing extra permissions
     */
    private function getExtraPermissions(): array
    {
        return array_map(fn($perm) => $this->formatPermission($perm), self::EXTRA_PERMISSIONS);
    }

    /**
     * Formats a permission string based on naming conventions.
     * 
     * @param string $perm permission
     *
     * @return string formatted permission
     */
    private function formatPermission(string $perm): string
    {
        $parts = array_filter(explode('_', $perm));
        $parts = array_map(fn($part) => lcfirst(trim($part)), $parts);
        return implode('_', $parts);
    }

    /**
     * Generates a list of all available permissions.
     *
     * @return array a flat array representing permissions
     */
    public function getPermissions(): array
    {
        return array_merge($this->getCrudPermissions(), $this->getExtraPermissions());
    }

    /**
     * Generates a grouped tree of all available permissions.
     *
     * @return array a nested array representing permissions
     */
    public function getGroupedPermissions(): array
    {
        return $this->groupPermissions($this->getPermissions());
    }

    /**
     * Groups a flat list of permission strings into a nested tree based on their prefixes.
     *
     * @param array $permissions flat list of permission names
     *
     * @return array nested grouped permissions
     */
    private function groupPermissions(array $permissions): array
    {
        $tree = [];
        foreach ($permissions as $permission) {
            $this->insertPermission($tree, $permission);
        }
        return $tree;
    }

    /**
     * Inserts a permission string into the correct location in the permission tree based on its prefix.
     *
     * @param array  &$tree      The current permission tree (passed by reference)
     * @param string $permission the permission name to insert
     */
    private function insertPermission(array &$tree, string $permission): void
    {
        foreach ($tree as $key => &$children) {
            if (str_starts_with($permission, $key . '_')) {
                $suffix = str_replace($key . '_', '', $permission);
                if (!$suffix) return;
                $this->insertPermission($children, $permission);
                return;
            }
        }
        $tree[$permission] = [];
    }
}
