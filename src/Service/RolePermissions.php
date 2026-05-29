<?php

namespace App\Service;

use App\Entity\Role;
use App\Entity\User;
use App\Security\Permission;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Manages role-based access permissions within the application,
 * particularly those associated with EasyAdmin CRUD controllers.
 *
 * Dynamically detects available permissions by scanning controller files,
 * compares role hierarchies, and groups permissions into a tree structure.
 *
 * Permission naming convention and registry: {@see Permission}.
 *
 * This class scans CrudControllers, builds the permission tree, and checks role maps.
 * Do not add permission names here; extend {@see Permission::EXTRA_PERMISSIONS},
 * {@see Permission::EXTRA_CRUD_ACTIONS}, or {@see Permission::DISABLED_CRUD_ACTIONS}.
 */
final readonly class RolePermissions
{
    /**
     * Paths relative to the project root where CrudControllers are scanned.
     */
    private const CRUD_PATHS = [
        '/src/Controller/Admin/Cruds',
    ];

    /**
     * Standard CRUD actions generated for every detected entity.
     */
    private const CRUD_ACTIONS = [
        Action::NEW,
        Action::DETAIL,
        Action::EDIT,
        Action::DELETE,
    ];

    public function __construct(
        private KernelInterface $kernel
    ) {
    }

    /**
     * Checks whether $role is equal or higher in permissions than $roleToCompare.
     *
     * Role A is considered "equal or higher" than role B when, for every permission
     * that B has enabled, A also has it enabled — i.e. A holds at least the same
     * permission set as B.
     *
     * @param Role $role          the base role
     * @param Role $roleToCompare the role to compare against
     *
     * @return bool true if $role holds at least the same permissions as $roleToCompare
     */
    public function isUp(Role $role, Role $roleToCompare): bool
    {
        if ($role === $roleToCompare) {
            return true;
        }

        foreach ($roleToCompare->getPermissions() as $permission => $value) {
            if ($value && !$this->roleHasPermission($role, $permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks whether a role has a specific permission enabled.
     *
     * @param Role   $role       the role to check
     * @param string $permission the permission identifier
     *
     * @return bool true if the permission exists and is enabled on the role
     */
    public function roleHasPermission(Role $role, string $permission): bool
    {
        return (bool) ($role->getPermissions()[Permission::normalize($permission)] ?? false);
    }

    /**
     * Checks whether a user has a specific permission enabled.
     *
     * @param User   $user       the user to check
     * @param string $permission the permission identifier
     *
     * @return bool true if the permission exists and is enabled on the user's role; false when the user has no role
     */
    public function userHasPermission(User $user, string $permission): bool
    {
        $role = $user->getRole();
        if ($role === null) {
            return false;
        }

        return $this->roleHasPermission($role, $permission);
    }

    /**
     * Equivalent to `userHasPermission($user, 'crud_'.$crud)` with normalised `$crud` (controller basename segment).
     *
     * @param string $crud entity key derived from `*CrudController.php` (e.g. `user` from `UserCrudController.php`)
     *
     * @return bool whether `crud_<entity>` exists and is enabled on the user's role
     */
    public function userHasPermissionCrud(User $user, string $crud): bool
    {
        return $this->userHasPermission($user, Permission::CRUD.'_'.$crud);
    }

    /**
     * Same as `userHasPermission($user, 'crud_'.$crud.'_'.$action)` with normalised segments (see `userHasPermissionCrud`).
     *
     * @param string $crud   entity segment as for `userHasPermissionCrud`
     * @param string $action EasyAdmin action name (e.g. `new`, `edit`, values from `Action::*` constants)
     *
     * @return bool whether the composed permission exists and is enabled on the user's role
     */
    public function userHasPermissionCrudAction(User $user, string $crud, string $action): bool
    {
        return $this->userHasPermission($user, Permission::CRUD.'_'.$crud.'_'.$action);
    }

    /**
     * Returns the flat list of all permissions available in the application.
     *
     * Includes CRUD permissions and extra permissions.
     *
     * @return array<string>
     */
    public function getPermissions(): array
    {
        return array_merge($this->getCrudPermissions(), Permission::EXTRA_PERMISSIONS);
    }

    /**
     * Returns the permission tree grouped by prefix.
     *
     * The result is a nested associative array where each key is the full permission
     * identifier and its value is an array of its direct children (same structure,
     * recursively).
     *
     * Example:
     *   [
     *     'crud_demoEntity' => [
     *       'crud_demoEntity_new' => []
     *     ]
     *   ]
     *
     * @return array<string, array>
     */
    public function getGroupedPermissions(): array
    {
        return $this->groupPermissions($this->getPermissions());
    }

    /**
     * Recursively traverses the permission tree and invokes a callback on every node.
     *
     * @param array<string, array> $permissions the permission tree
     * @param callable             $callback    called as ($permission, $parent, $level) for each node
     * @param string|null          $parent      full identifier of the parent node (null at the root)
     * @param int                  $level       current depth level (starts at 0)
     */
    public function loopPermissions(array $permissions, callable $callback, ?string $parent = null, int $level = 0): void
    {
        foreach ($permissions as $permission => $children) {
            $callback($permission, $parent, $level);
            if (!empty($children)) {
                $this->loopPermissions($children, $callback, $permission, $level + 1);
            }
        }
    }

    /**
     * Builds the flat list of CRUD permissions by scanning controller files.
     *
     * For each CrudController found in the configured paths, the following permissions
     * are generated, unless excluded by {@see Permission::DISABLED_CRUD_ACTIONS}:
     *   - Entity CRUD access permission (crud_<entity>).
     *   - One permission per standard action (crud_<entity>_<action>).
     *
     * Extra CRUD action permissions declared in Permission::EXTRA_CRUD_ACTIONS are appended
     * exactly once, regardless of how many scan paths are configured.
     *
     * @return array<string>
     */
    private function getCrudPermissions(): array
    {
        $permissions = [];
        $disabled = Permission::disabledCrudKeys();

        foreach (self::CRUD_PATHS as $relativePath) {
            $absolutePath = $this->kernel->getProjectDir().rtrim($relativePath, '/');

            if (!is_dir($absolutePath)) {
                continue;
            }

            $finder = new Finder();
            $finder->files()->in($absolutePath)->name('*CrudController.php');

            foreach ($finder as $file) {
                $crudName = Permission::normalize(
                    str_replace('CrudController.php', '', $file->getFilename())
                );

                if (in_array($crudName, $disabled, true)) {
                    continue;
                }

                $permissions[] = Permission::CRUD.'_'.$crudName;

                foreach (self::CRUD_ACTIONS as $action) {
                    $actionName = Permission::normalize($action);
                    $fullName = $crudName.'_'.$actionName;

                    if (in_array($fullName, $disabled, true)) {
                        continue;
                    }

                    $permissions[] = Permission::CRUD.'_'.$fullName;
                }
            }
        }

        // Extra permissions are appended once, outside the path loop.
        foreach ($this->getExtraCrudActionPermissions() as $extra) {
            $permissions[] = Permission::CRUD.'_'.$extra;
        }

        return $permissions;
    }

    /**
     * Returns the list of extra CRUD action permissions, normalised.
     *
     * @return array<string>
     */
    private function getExtraCrudActionPermissions(): array
    {
        $permissions = [];

        foreach (Permission::EXTRA_CRUD_ACTIONS as $crud => $actions) {
            foreach ($actions as $action) {
                $permissions[] = Permission::normalize($crud.'_'.$action);
            }
        }

        return $permissions;
    }

    /**
     * Groups a flat list of permission identifiers into a nested tree based on their prefixes.
     *
     * @param array<string> $permissions flat list of permission identifiers
     *
     * @return array<string, array>
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
     * Inserts a permission into the correct position in the tree based on its prefix.
     *
     * A permission becomes a child of another if its full name starts with the parent's
     * name followed by an underscore. Children are stored using their full identifier as
     * key to allow direct lookup anywhere in the tree.
     *
     * @param array<string, array> $tree       the current permission tree (passed by reference)
     * @param string               $permission the permission identifier to insert
     */
    private function insertPermission(array &$tree, string $permission): void
    {
        foreach ($tree as $key => &$children) {
            if (str_starts_with($permission, $key.'_')) {
                $this->insertPermission($children, $permission);

                return;
            }
        }

        $tree[$permission] = [];
    }
}
