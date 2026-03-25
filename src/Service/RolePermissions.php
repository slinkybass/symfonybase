<?php

namespace App\Service;

use App\Entity\Role;
use App\Entity\User;
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
 * Permission naming convention:
 *   - Entity CRUD access:   crud_<entity>            (e.g. crud_user, crud_demoEntity)
 *   - Entity CRUD action:   crud_<entity>_<action>   (e.g. crud_user_new, crud_demoEntity_new)
 *   - Extra CRUD actions:   crud_<entity>_<action>   (e.g. crud_user_impersonate)
 *   - Non-CRUD permissions: <name>                   (e.g. media)
 *
 * Identifiers are normalised by splitting on underscores, applying lcfirst to each
 * part, and rejoining with underscores. This preserves internal camelCase so that
 * multi-word entity names remain unambiguous (e.g. 'DemoEntity' -> 'demoEntity').
 */
class RolePermissions
{
    public const CRUD_PREFIX = 'crud';

    /**
     * Paths relative to the project root where CrudControllers are scanned.
     */
    private const CRUD_PATHS = [
        '/src/Controller/Admin/Cruds',
    ];

    /**
     * CRUD permissions that must be excluded from the permission system.
     * Useful for entities that do not expose all four standard actions,
     * or whose actions should not be controlled individually.
     *
     * Format: '<entity>' or '<entity>_<action>'.
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
     * Additional CRUD actions not derived from any controller file.
     * Appended with the CRUD prefix and included exactly once.
     *
     * Format: '<entity>_<action>'.
     */
    private const EXTRA_CRUD_ACTION_PERMISSIONS = [
        'admin_impersonate',
        'user_impersonate',
    ];

    /**
     * Additional permissions unrelated to EasyAdmin CRUDs.
     */
    private const EXTRA_PERMISSIONS = [
        'media',
        'media_tree',
        'media_upload',
        'media_edit',
        'media_folders',
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

    public function __construct(private readonly KernelInterface $kernel)
    {
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
        return (bool) ($role->getPermissions()[$this->normalizePermission($permission)] ?? false);
    }

    /**
     * Checks whether a user has a specific permission enabled.
     *
     * @param User   $user       the user to check
     * @param string $permission the permission identifier
     *
     * @return bool true if the permission exists and is enabled on the user's role
     */
    public function userHasPermission(User $user, string $permission): bool
    {
        return $this->roleHasPermission($user->getRole(), $permission);
    }

    /**
     * Checks whether a user has access to the CRUD of a given entity.
     *
     * Equivalent to userHasPermission($user, 'crud_<entity>').
     *
     * @param User   $user the user to check
     * @param string $crud the entity name
     *
     * @return bool true if the CRUD permission exists and is enabled on the user's role
     */
    public function userHasPermissionCrud(User $user, string $crud): bool
    {
        return $this->userHasPermission($user, self::CRUD_PREFIX.'_'.$crud);
    }

    /**
     * Checks whether a user has access to a specific action within a CRUD.
     *
     * Equivalent to userHasPermission($user, 'crud_<entity>_<action>').
     *
     * @param User   $user   the user to check
     * @param string $crud   the entity name
     * @param string $action the CRUD action
     *
     * @return bool true if the CRUD action permission exists and is enabled on the user's role
     */
    public function userHasPermissionCrudAction(User $user, string $crud, string $action): bool
    {
        return $this->userHasPermission($user, self::CRUD_PREFIX.'_'.$crud.'_'.$action);
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
        return array_merge($this->getCrudPermissions(), $this->getExtraPermissions());
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
     * are generated, unless that permission is listed in DISABLED_CRUD_PERMISSIONS:
     *   - Entity CRUD access permission (crud_<entity>).
     *   - One permission per standard action (crud_<entity>_<action>).
     *
     * Extra CRUD action permissions (EXTRA_CRUD_ACTION_PERMISSIONS) are appended
     * exactly once, regardless of how many scan paths are configured.
     *
     * @return array<string>
     */
    private function getCrudPermissions(): array
    {
        $permissions = [];
        $disabled = $this->getDisabledCrudPermissions();

        foreach (self::CRUD_PATHS as $relativePath) {
            $absolutePath = $this->kernel->getProjectDir().rtrim($relativePath, '/');

            if (!is_dir($absolutePath)) {
                continue;
            }

            $finder = new Finder();
            $finder->files()->in($absolutePath)->name('*CrudController.php');

            foreach ($finder as $file) {
                $crudName = $this->normalizePermission(
                    str_replace('CrudController.php', '', $file->getFilename())
                );

                if (in_array($crudName, $disabled, true)) {
                    continue;
                }

                $permissions[] = self::CRUD_PREFIX.'_'.$crudName;

                foreach (self::CRUD_ACTIONS as $action) {
                    $actionName = $this->normalizePermission($action);
                    $fullName = $crudName.'_'.$actionName;

                    if (in_array($fullName, $disabled, true)) {
                        continue;
                    }

                    $permissions[] = self::CRUD_PREFIX.'_'.$fullName;
                }
            }
        }

        // Extra permissions are appended once, outside the path loop.
        foreach ($this->getExtraCrudActionPermissions() as $extra) {
            $permissions[] = self::CRUD_PREFIX.'_'.$extra;
        }

        return $permissions;
    }

    /**
     * Returns the list of disabled CRUD permissions, normalised.
     *
     * @return array<string>
     */
    private function getDisabledCrudPermissions(): array
    {
        return array_map($this->normalizePermission(...), self::DISABLED_CRUD_PERMISSIONS);
    }

    /**
     * Returns the list of extra CRUD action permissions, normalised.
     *
     * @return array<string>
     */
    private function getExtraCrudActionPermissions(): array
    {
        return array_map($this->normalizePermission(...), self::EXTRA_CRUD_ACTION_PERMISSIONS);
    }

    /**
     * Returns the list of extra permissions, normalised.
     *
     * @return array<string>
     */
    private function getExtraPermissions(): array
    {
        return array_map($this->normalizePermission(...), self::EXTRA_PERMISSIONS);
    }

    /**
     * Normalises a permission identifier by splitting on underscores, applying
     * lcfirst to each part, and rejoining with underscores.
     *
     * This preserves internal camelCase on multi-word entity names while ensuring
     * the first character of each segment is lowercased:
     *   'DemoEntity'  -> 'demoEntity'
     *   'New'         -> 'new'
     *   'config_Edit' -> 'config_edit'
     *
     * @param string $permission the raw identifier to normalise
     *
     * @return string the normalised identifier
     */
    private function normalizePermission(string $permission): string
    {
        $parts = array_filter(explode('_', $permission));
        $parts = array_map(lcfirst(...), $parts);

        return implode('_', $parts);
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
