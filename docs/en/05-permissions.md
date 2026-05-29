# Permissions

The base ships its own permission system on top of Symfony's roles.

**Two layers (do not mix them up):**

| Layer           | Mechanism                                                         | What it gates                                  |
| --------------- | ----------------------------------------------------------------- | ---------------------------------------------- |
| Firewall        | `ROLE_ADMIN` from `Role::isAdmin` + `access_control` on `^/admin` | Entering the admin area at all                 |
| Application map | JSON `permissions` on `Role`, checked via `PermissionVoter`       | Menus, CRUD actions, custom admin routes, Twig |

Symfony roles (`ROLE_USER`, `ROLE_ADMIN`, …) still drive firewall access. The permission map decides **what each role can see and do inside EasyAdmin** (per CRUD, per action) and on other admin routes.

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

Permission ids are stored and used with underscore notation end-to-end (`crud_user`, `media_upload`).

| Pattern                       | Example                 | Source                                      |
| ----------------------------- | ----------------------- | ------------------------------------------- |
| `crud_<entity>`               | `crud_user`             | Existence of `UserCrudController.php`       |
| `crud_<entity>_<action>`      | `crud_user_edit`        | Auto-generated for `new/detail/edit/delete` |
| `crud_<entity>_<extraAction>` | `crud_user_impersonate` | `Permission::EXTRA_CRUD_ACTIONS`            |
| `<custom>`                    | `media_upload`          | `Permission::EXTRA_PERMISSIONS`             |

Identifiers are normalized by splitting on `_`, lowercasing the first letter of each segment, and rejoining: `DemoEntity` → `demoEntity`, `config_Edit` → `config_edit`. This preserves multi-word camelCase entities.

### Registry (single place)

All starter-specific permission declarations live in `src/Security/Permission.php`:

- `Permission::EXTRA_PERMISSIONS` — non-CRUD permissions (`media`, `media_upload`, …).
- `Permission::EXTRA_CRUD_ACTIONS` — custom CRUD actions (`admin => impersonate`, …).
- `Permission::DISABLED_CRUD_ACTIONS` — standard actions to exclude per entity (`config => new/edit/…`, `settings => …`).

`RolePermissions` only scans CrudControllers and applies this registry; it does not define permission names.

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

`isUp()` compares two roles as flat maps: role **A** is “up” from **B** when every permission enabled on **B** is also enabled on **A**. It is used in CRUDs (e.g. `AdminCrudController`, `RoleCrudController`, impersonate actions) to forbid acting on users or roles that outrank the current user.

When saving a role in `RoleCrudController`, a second pass over the permission tree forces any child to `false` if its parent is `false` (server-side, independent of the hierarchical toggles in the browser).

## `App\Security\Permission`

`src/Security/Permission.php`. Small builder for permission ids used by `isGranted()` and EasyAdmin `setPermission()`. It hides the string format and mirrors the ids persisted on roles.

```php
Permission::MEDIA
Permission::ACTION_IMPERSONATE
Permission::crud('user')                              // crud_user
Permission::crudAction('admin', Permission::ACTION_IMPERSONATE)  // crud_admin_impersonate
```

Use constants or `crud()` / `crudAction()` from controllers and menus. Register new permissions only in `Permission.php` (`EXTRA_*`, `DISABLED_CRUD_ACTIONS`, optional constants).

## `App\Security\Voter\PermissionVoter`

`src/Security/Voter/PermissionVoter.php`. Adapts Symfony authorization checks to `RolePermissions::userHasPermission()`. Any supported `isGranted()` / `#[IsGranted(...)]` / EasyAdmin `setPermission()` call goes through this voter and is matched against the stored permission ids.

## `App\Security\VirtualPermission`

`src/Security/VirtualPermission.php`. EasyAdmin's `setPermission()` takes a Symfony role/attribute string. To **deny** or hide something based on a plain boolean / contextual rule (not a persisted application permission), the base passes a sentinel value (`NOPERMISSION`) that no real role will ever hold:

```php
$actions->setPermissions(array_fill_keys($denied, VirtualPermission::DENY));
$field->setPermission(VirtualPermission::allowed($visible));
```

`VirtualPermission::allowed(bool)` returns `''` (no restriction) or `DENY`. Keep it for UI-level booleans such as `displayIf(bool)` or contextual deny rules that cannot be represented as a reusable application permission.

## Wiring inside CRUDs

`App\Controller\Admin\AbstractCrudController` exposes `permission()`, `permissionCrud()`, and `permissionCrudAction()` helpers to build permission ids, plus `hasPermission*()` wrappers over `isGranted()`. `configureActions()` denies `INDEX/NEW/DETAIL/EDIT/DELETE/BATCH_DELETE` when the matching `crud_*` / `crud_*_<action>` permission is missing. If `crud_<entity>` itself is missing, **all** standard actions are denied (same rule as the base class; custom CRUDs such as `AdminCrudController` must keep this when overriding `configureActions()`).

Subclasses add finer rules: `AdminCrudController` / `UserCrudController` use `isUp()` on edit/delete/impersonate; `RoleCrudController` only renders permission switches the editor already holds and blocks editing one's own role.

## Twig helpers

`App\Twig\RolePermissionsExtension` (`src/Twig/RolePermissionsExtension.php`) exposes:

```twig
{% if has_permission('media_upload') %} ... {% endif %}
{% if has_permission_crud('user') %} ... {% endif %}
{% if has_permission_crud_action('user', 'edit') %} ... {% endif %}
```

A non-`User` security token (e.g. anonymous) is treated as having no permissions. In Twig you can also use Symfony's `is_granted('media')` for the **current** user — it hits the same `PermissionVoter`. Use `has_permission(..., otherUser)` when you need to check someone else's role (rare).

## Applying permissions (cookbook)

Register every new permission id in `Permission.php` first (`EXTRA_PERMISSIONS`, `EXTRA_CRUD_ACTIONS`, and/or a new `*CrudController.php`). Then run `php bin/console app:update-permissions` so `ROLE_SUPERADMIN` receives the new keys (see [console](16-console.md)).

Prefer **`Permission::…` constants / `crud()` / `crudAction()`** plus **`isGranted()`** in PHP so ids stay normalized and the voter stays the single gate.

### Custom admin routes (outside EasyAdmin CRUD)

`src/Controller/AdminController.php` — attribute on the action:

```php
use App\Security\Permission;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminRoute('/media', name: 'media')]
#[IsGranted(Permission::MEDIA)]
public function media(): void
{
}
```

Equivalent inside a controller method: `$this->denyAccessUnlessGranted(Permission::MEDIA);`

### EasyAdmin sidebar menu

`DashboardController::configureMenuItems()` — pass the permission id to the menu item; use `isGranted()` only when you need to count visible items (e.g. submenu grouping):

```php
$permissionMedia = Permission::MEDIA;

yield MenuItem::linkToRoute('…', 'file', 'admin_media')
    ->setPermission($permissionMedia);

// Optional: layout logic
if ($this->isGranted($permissionMedia)) { … }
```

CRUD menu entries use `Permission::crud('user')` the same way (`setPermission($permissionUser)`).

### Standard CRUD actions (new / edit / delete / index)

Inherited from `AbstractCrudController::configureActions()` when the subclass calls `parent::configureActions($actions)` (e.g. `UserCrudController`, `RoleCrudController`). No extra code unless you need exceptions.

Mapping:

- `crud_<entity>` → list (`INDEX`) and base access.
- `crud_<entity>_new`, `_detail`, `_edit`, `_delete` → matching actions (+ `BATCH_DELETE` follows delete).

### Custom CRUD actions (e.g. impersonate)

1. Add the action name under `Permission::EXTRA_CRUD_ACTIONS` for the entity key (`admin`, `user`, …).
2. Run `app:update-permissions`.
3. Wire EasyAdmin:

```php
$impersonate = Action::new('impersonate', '…')
    ->linkToUrl(fn (User $u) => $this->generateUrl('home', ['_switch_user' => $u->getEmail()]))
    ->displayIf(fn (User $u) => $user !== $u
        && $u->isActive()
        && $this->rolePermissions->isUp($user->getRole(), $u->getRole()));

$actions->add(Crud::PAGE_INDEX, $impersonate);
$actions->setPermission('impersonate', $this->permissionCrudAction(Permission::ACTION_IMPERSONATE));
```

`setPermission()` enforces the persisted permission; `displayIf()` adds contextual rules (`isUp`, own user, etc.).

### Overriding `configureActions()` in a subclass

Call `parent::configureActions($actions)` when you can. If you replace the deny list entirely (e.g. `AdminCrudController`), when `!$this->hasPermissionCrud()` deny at least `INDEX`, `NEW`, `DETAIL`, `EDIT`, `DELETE`, and `BATCH_DELETE` — not only `INDEX`.

Hide actions without a dedicated permission id:

```php
$actions->setPermissions(array_fill_keys($denied, VirtualPermission::DENY));
```

### Fields and conditional UI in CRUDs

`FieldGenerator` / `FieldTrait::displayIf(bool)` maps to `VirtualPermission::allowed($visible)` — config flags, not role permissions:

```php
$field->displayIf($this->config()->enablePublic);
```

For a **role** permission on a field, use `setPermission($this->permissionCrudAction(Action::EDIT))` on the field DTO if needed.

### Checks inside a CRUD or service

```php
// In any AbstractCrudController subclass
if ($this->hasPermissionCrud()) { … }
if ($this->hasPermissionCrudAction(Action::EDIT)) { … }
if ($this->hasPermission(Permission::MEDIA_UPLOAD)) { … }

// Injected RolePermissions (e.g. custom controller)
$this->rolePermissions->userHasPermission($user, Permission::MEDIA);
$this->rolePermissions->isUp($editorRole, $targetRole);
```

### Twig templates

Current user (same as `isGranted`):

```twig
{% if is_granted(constant('App\\Security\\Permission::MEDIA')) %}…{% endif %}
{% if has_permission_crud('user') %}…{% endif %}
```

Another user:

```twig
{% if has_permission('media_upload', someUser) %}…{% endif %}
```

### What not to use application permissions for

- **Firewall-only concerns** — keep using `ROLE_ADMIN`, `IS_AUTHENTICATED`, `IS_IMPERSONATOR` in `security.yaml` / `AccessSubscriber`.
- **One-off booleans** — `VirtualPermission::allowed()` or `VirtualPermission::DENY`, not a new JSON key.
- **Hiding without enforcing** — always pair `displayIf()` on sensitive custom actions with `setPermission()` when a persisted permission exists.

## Admin principal helper

`App\Security\AdminUserTrait` (`src/Security/AdminUserTrait.php`) exposes `user(): User` for controllers running under `^/admin`. It throws `LogicException` when the token is not an `App\Entity\User` (a misuse: the firewall guarantees `ROLE_ADMIN` and therefore an `App\Entity\User`).
