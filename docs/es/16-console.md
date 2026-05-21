# Comandos de consola

`src/Command/` incluye tres comandos personalizados. Todos extienden `Symfony\Component\Console\Command\Command` y usan `#[AsCommand(name: 'app:...')]`.

## `app:create-users`

`src/Command/CreateUsersCommand.php`. Inicialización idempotente de roles y usuarios por defecto.

Roles creados si no existen:

- `ROLE_SUPERADMIN` — rol de administrador con el árbol de permisos **completo** escaneado y habilitado.
- `ROLE_ADMIN` — rol de administrador sin permisos iniciales.
- `ROLE_USER` — rol sin privilegios de administrador.

Usuarios creados si no existe ya un usuario para ese rol:

| Email                       | Contraseña   | Rol               |
| --------------------------- | ------------ | ----------------- |
| `superadmin@superadmin.com` | `superadmin` | `ROLE_SUPERADMIN` |
| `admin@admin.com`           | `admin`      | `ROLE_ADMIN`      |

> Cambia estas contraseñas en cualquier entorno que no sea trivial.

Ejecutar tras crear el esquema:

```bash
php bin/console app:create-users
```

## `app:update-permissions`

`src/Command/UpdatePermissionsCommand.php`. Recalcula el mapa de permisos para `ROLE_SUPERADMIN` a partir del árbol activo de [`RolePermissions`](05-permissions.md) (escaneo de CRUDs + extras) y lo persiste.

Ejecutar cada vez que añadas o elimines un CRUD o modifiques las constantes `EXTRA_*` en `RolePermissions`:

```bash
php bin/console app:update-permissions
```

## `app:demo`

`src/Command/DemoCommand.php`. Activa o desactiva la entidad de demo opcional incluida en `docs/Demo/`. El comando:

1. Detecta el estado actual buscando `docs/Demo/DemoEntity.phps` (su presencia indica que la demo está actualmente desactivada).
2. Renombra tres pares de archivos entre su copia de respaldo `.phps` en `docs/Demo/` y la ubicación activa `.php` en `src/`:
    - `Entity/DemoEntity.php`
    - `Controller/Admin/Cruds/DemoEntityCrudController.php`
    - `Form/Type/DemoEntityType.php`
3. Ejecuta `doctrine:schema:update --force`.
4. Ejecuta `app:update-permissions` para que los nuevos permisos `crud_demoEntity*` lleguen a `ROLE_SUPERADMIN`.

```bash
php bin/console app:demo
```
