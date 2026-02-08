<?php

namespace App\Service;

use App\Entity\Role;
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

    /**
     * @const array
     * Additional CRUD-related permissions not derived from controllers.
     */
    public const CRUD_PERMISSIONS = [
        'crudAdminImpersonate',
        'crudUserImpersonate',
    ];

    /**
     * @const array
     * List of CRUD-related permissions that should be excluded from permissions.
     */
    public const DISABLED_CRUD_PERMISSIONS = [
        'crudConfigNew',
        'crudConfigEdit',
        'crudConfigDetail',
        'crudConfigDelete',
        'crudSettingsNew',
        'crudSettingsEdit',
        'crudSettingsDetail',
        'crudSettingsDelete',
    ];

    /**
     * @const array
     * Additional permissions.
     */
    public const EXTRA_PERMISSIONS = [
        'media',
        'mediaTree',
        'mediaUpload',
        'mediaEdit',
        'mediaFolders',
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
        if ($role == $roleToCompare) {
            return true;
        }
        if (!count($role->getPermissions())) {
            return false;
        }
        foreach ($role->getPermissions() as $permissionName => $permissionValue) {
            if (!$permissionValue && $roleToCompare->getPermission($permissionName)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Generates a flat list of available CRUD-related permissions
     * by scanning controller files and applying naming conventions.
     *
     * @return array a flat array representing permissions
     */
    public function getCrudPermissions(): array
    {
        $finder = new Finder();
        $crudsPath = $this->kernel->getProjectDir() . '/src/Controller/Admin/Cruds';
        if (!file_exists($crudsPath)) {
            return self::CRUD_PERMISSIONS;
        }
        $finder->files()->in($crudsPath)->name('*.php');

        $crudPermissions = [];
        foreach ($finder as $file) {
            $fileName = $file->getFilename();
            if (!str_ends_with($fileName, 'CrudController.php')) {
                continue;
            }
            $crudName = ucfirst(str_replace('CrudController.php', '', $fileName));
            $permName = 'crud' . $crudName;
            if (in_array($permName, self::DISABLED_CRUD_PERMISSIONS)) {
                continue;
            }
            $crudPermissions[] = $permName;
            $actionNames = [Action::NEW, Action::DETAIL, Action::EDIT, Action::DELETE];
            foreach ($actionNames as $actionName) {
                $actionName = ucfirst($actionName);
                $subPermName = 'crud' . $crudName . $actionName;
                if (in_array($subPermName, self::DISABLED_CRUD_PERMISSIONS)) {
                    continue;
                }
                $crudPermissions[] = $subPermName;
            }
        }
        return array_merge($crudPermissions, self::CRUD_PERMISSIONS);
    }

    /**
     * Generates a flat list of available extra permissions.
     *
     * @return array a flat array representing permissions
     */
    public function getExtraPermissions(): array
    {
        return self::EXTRA_PERMISSIONS;
    }

    /**
     * Generates a flat list of all available permissions.
     *
     * @return array a flat array representing permissions
     */
    public function getPermissions(): array
    {
        $crudPermissions = $this->getCrudPermissions();
        $extraPermissions = $this->getExtraPermissions();
        return array_merge($crudPermissions, $extraPermissions);
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
            if (str_starts_with($permission, $key)) {
                $suffix = substr($permission, strlen($key));
                if ($suffix === '') {
                    return;
                }
                $this->insertPermission($children, $permission);
                return;
            }
        }
        $tree[$permission] = [];
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
}
