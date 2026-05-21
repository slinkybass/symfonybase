# Permissions

The base ships its own permission system on top of Symfony's roles. Symfony roles (`ROLE_USER`, `ROLE_ADMIN`, ...) still drive firewall access; the permission map below decides **what each role can see and do inside EasyAdmin** (per CRUD, per action) and inside arbitrary application areas.

## `App\Entity\Role`

`src/Entity/Role.php`. Stores:

- `name` (e.g. `ROLE_SUPERADMIN`) — the Symfony role string.
- `displayName` — human label; setting it before the entity has an id also derives `name` as `ROLE_<UPPER(displayName)>`.
- `isAdmin` (bool) — when true, `User::getRoles()` adds `ROLE_ADMIN`, unlocking `^/admin`.
- `permissions` (JSON column) — `<permissionId> => bool` map.
- One-to-many to `User` (`mappedBy: 'role'`).

A `User` always has exactly one `Role` (DB-level `nullable: false` on the join column). `User::getRoles()` defends against a transient null role.

## `App\Service\RolePermissions`

`src/Service/RolePermissions.php`. Single source of truth for the permission list and the per-user / per-role checks. Built dynamically by scanning `src/Controller/Admin/Cruds/*CrudController.php` with Symfony Finder.

### Permission identifier conventions

| Pattern                       | Example                 | Source                                      |
| ----------------------------- | ----------------------- | ------------------------------------------- |
| `crud_<entity>`               | `crud_user`             | Existence of `UserCrudController.php`       |
| `crud_<entity>_<action>`      | `crud_user_new`         | Auto-generated for `new/detail/edit/delete` |
| `crud_<entity>_<extraAction>` | `crud_user_impersonate` | `EXTRA_CRUD_ACTION_PERMISSIONS`             |
| `<custom>`                    | `media`, `media_upload` | `EXTRA_PERMISSIONS`                         |

Identifiers are normalized by splitting on `_`, lowercasing the first letter of each segment, and rejoining: `DemoEntity` → `demoEntity`, `config_Edit` → `config_edit`. This preserves multi-word camelCase entities.

### Disabled / extra constants

Defined as private constants on the class:

- `DISABLED_CRUD_PERMISSIONS` excludes per-action permissions for configuration-style CRUDs (`config_*`, `settings_*`).
- `EXTRA_CRUD_ACTION_PERMISSIONS` adds custom CRUD actions (`admin_impersonate`, `user_impersonate`).
- `EXTRA_PERMISSIONS` adds non-CRUD permissions (`media`, `media_tree`, `media_upload`, `media_edit`, `media_folders`).

Adding a new CRUD: drop `FooCrudController.php` under `src/Controller/Admin/Cruds/` and re-run `app:update-permissions` (see [console](16-console.md)) to refresh `ROLE_SUPERADMIN`.

### Permission tree

`getGroupedPermissions()` returns a nested array keyed by full identifier where children prefix-match their parent (`crud_user_new` is a child of `crud_user`). `loopPermissions()` walks the tree and reports `(permission, parent, level)` for each node — used by `RoleCrudController` to render the permission grid with hierarchical toggles.

### Public API

```php
$rolePermissions->roleHasPermission(Role $role, string $permission): bool
$rolePermissions->userHasPermission(User $user, string $permission): bool
$rolePermissions->userHasPermissionCrud(User $user, string $crud): bool
$rolePermissions->userHasPermissionCrudAction(User $user, string $crud, string $action): bool
$rolePermissions->isUp(Role $a, Role $b): bool          // a holds at least every permission b enables
$rolePermissions->getPermissions(): array               // flat list (CRUD scan + extras)
$rolePermissions->getGroupedPermissions(): array        // nested tree
```

`isUp()` is used in CRUDs (e.g. `AdminCrudController`) to forbid editing or deleting users whose role outranks the current user.

## `App\Security\VirtualPermission`

`src/Security/VirtualPermission.php`. EasyAdmin's `setPermission()` takes a Symfony role string. To **deny** an action without defining a real role, the base passes a sentinel value (`NOPERMISSION`) that no real role will ever hold:

```php
$actions->setPermissions(array_fill_keys($denied, VirtualPermission::DENY));
$action->setPermission(VirtualPermission::allowed($hasPermissionImpersonate));
```

`VirtualPermission::allowed(bool)` returns `''` (no restriction) or `DENY`. Use `VirtualPermission::isDenied()` when reading back permissions already attached to a menu/action.

## Wiring inside CRUDs

`App\Controller\Admin\AbstractCrudController::configureActions()` denies `INDEX/NEW/DETAIL/EDIT/DELETE/BATCH_DELETE` whose `crud_*` / `crud_*_<action>` permission is missing for the current user. Subclasses extend this with finer-grained rules (e.g. `AdminCrudController` enforces `isUp()` between user roles before allowing edit/delete and adds an `impersonate` action gated by `crud_admin_impersonate`).

## Twig helpers

`App\Twig\RolePermissionsExtension` (`src/Twig/RolePermissionsExtension.php`) exposes:

```twig
{% if has_permission('media_upload') %} ... {% endif %}
{% if has_permission_crud('user') %} ... {% endif %}
{% if has_permission_crud_action('user', 'edit') %} ... {% endif %}
```

A non-`User` security token (e.g. anonymous) is treated as having no permissions.

## Admin principal helper

`App\Security\AdminUserTrait` (`src/Security/AdminUserTrait.php`) exposes `user(): User` for controllers running under `^/admin`. It throws `LogicException` when the token is not an `App\Entity\User` (a misuse: the firewall guarantees `ROLE_ADMIN` and therefore an `App\Entity\User`).
