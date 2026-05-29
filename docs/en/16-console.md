# Console commands

`src/Command/` ships three custom commands. All extend `Symfony\Component\Console\Command\Command` and use `#[AsCommand(name: 'app:...')]`.

## `app:create-users`

`src/Command/CreateUsersCommand.php`. Idempotent bootstrap of roles and default users.

Roles created if missing:

- `ROLE_SUPERADMIN` — admin role with the **full** scanned permission tree enabled.
- `ROLE_ADMIN` — admin role with no permissions seeded.
- `ROLE_USER` — non-admin role.

Users created if no user already exists for that role:

| Email                       | Password     | Role              |
| --------------------------- | ------------ | ----------------- |
| `superadmin@superadmin.com` | `superadmin` | `ROLE_SUPERADMIN` |
| `admin@admin.com`           | `admin`      | `ROLE_ADMIN`      |

> Replace these passwords on any non-trivial environment.

Run after the schema is created:

```bash
php bin/console app:create-users
```

## `app:update-permissions`

`src/Command/UpdatePermissionsCommand.php`. Recomputes the permission map for `ROLE_SUPERADMIN` from the live [`RolePermissions`](05-permissions.md) tree (CRUD scan + extras) and persists it.

Run this every time you add / remove a CRUD or change the `EXTRA_*` / `DISABLED_CRUD_ACTIONS` constants in `Permission.php`:

```bash
php bin/console app:update-permissions
```

## `app:demo`

`src/Command/DemoCommand.php`. Toggles the optional demo entity bundled under `docs/Demo/`. The command:

1. Detects the current state by looking for `docs/Demo/DemoEntity.phps` (its presence means the demo is currently disabled).
2. Renames three pairs of files between their `.phps` backup under `docs/Demo/` and the live `.php` location under `src/`:
    - `Entity/DemoEntity.php`
    - `Controller/Admin/Cruds/DemoEntityCrudController.php`
    - `Form/Type/DemoEntityType.php`
3. Runs `doctrine:schema:update --force`.
4. Runs `app:update-permissions` so the new `crud_demoEntity*` permissions reach `ROLE_SUPERADMIN`.

```bash
php bin/console app:demo
```
