# Permisos

La base incluye su propio sistema de permisos sobre los roles de Symfony. Los roles de Symfony (`ROLE_USER`, `ROLE_ADMIN`, ...) siguen controlando el acceso al firewall; el mapa de permisos que se describe a continuación decide **qué puede ver y hacer cada rol dentro de EasyAdmin** (por CRUD, por acción) y dentro de áreas arbitrarias de la aplicación.

## `App\Entity\Role`

`src/Entity/Role.php`. Almacena:

- `name` (p. ej. `ROLE_SUPERADMIN`) — la cadena de rol de Symfony.
- `displayName` — etiqueta legible; establecerla antes de que la entidad tenga un id también deriva `name` como `ROLE_<UPPER(displayName)>`.
- `isAdmin` (bool) — cuando es `true`, `User::getRoles()` añade `ROLE_ADMIN`, desbloqueando `^/admin`.
- `permissions` (columna JSON) — mapa `<permissionId> => bool`.
- Relación uno-a-muchos con `User` (`mappedBy: 'role'`).

Un `User` siempre tiene exactamente un `Role` (`nullable: false` a nivel de BD en la columna de unión). `User::getRoles()` se protege frente a un rol nulo transitorio.

## `App\Service\RolePermissions`

`src/Service/RolePermissions.php`. Fuente única de verdad para la lista de permisos y las comprobaciones por usuario / por rol. Se construye dinámicamente escaneando `src/Controller/Admin/Cruds/*CrudController.php` con Symfony Finder.

### Convenciones de identificadores de permisos

| Patrón                        | Ejemplo                 | Origen                                      |
| ----------------------------- | ----------------------- | ------------------------------------------- |
| `crud_<entity>`               | `crud_user`             | Existencia de `UserCrudController.php`      |
| `crud_<entity>_<action>`      | `crud_user_new`         | Generado automáticamente para `new/detail/edit/delete` |
| `crud_<entity>_<extraAction>` | `crud_user_impersonate` | `EXTRA_CRUD_ACTION_PERMISSIONS`             |
| `<custom>`                    | `media`, `media_upload` | `EXTRA_PERMISSIONS`                         |

Los identificadores se normalizan dividiendo por `_`, poniendo en minúscula la primera letra de cada segmento y volviendo a unir: `DemoEntity` → `demoEntity`, `config_Edit` → `config_edit`. Esto preserva las entidades camelCase de varias palabras.

### Constantes de deshabilitado / extra

Definidas como constantes privadas en la clase:

- `DISABLED_CRUD_PERMISSIONS` excluye los permisos por acción para CRUDs de tipo configuración (`config_*`, `settings_*`).
- `EXTRA_CRUD_ACTION_PERMISSIONS` añade acciones CRUD personalizadas (`admin_impersonate`, `user_impersonate`).
- `EXTRA_PERMISSIONS` añade permisos no-CRUD (`media`, `media_tree`, `media_upload`, `media_edit`, `media_folders`).

Para añadir un nuevo CRUD: coloca `FooCrudController.php` en `src/Controller/Admin/Cruds/` y vuelve a ejecutar `app:update-permissions` (ver [console](16-console.md)) para actualizar `ROLE_SUPERADMIN`.

### Árbol de permisos

`getGroupedPermissions()` devuelve un array anidado indexado por identificador completo donde los hijos coinciden en prefijo con su padre (`crud_user_new` es hijo de `crud_user`). `loopPermissions()` recorre el árbol e informa de `(permission, parent, level)` para cada nodo — usado por `RoleCrudController` para renderizar la cuadrícula de permisos con toggles jerárquicos.

### API pública

```php
$rolePermissions->roleHasPermission(Role $role, string $permission): bool
$rolePermissions->userHasPermission(User $user, string $permission): bool
$rolePermissions->userHasPermissionCrud(User $user, string $crud): bool
$rolePermissions->userHasPermissionCrudAction(User $user, string $crud, string $action): bool
$rolePermissions->isUp(Role $a, Role $b): bool          // a tiene al menos todos los permisos que b habilita
$rolePermissions->getPermissions(): array               // lista plana (escaneo CRUD + extras)
$rolePermissions->getGroupedPermissions(): array        // árbol anidado
```

`isUp()` se usa en los CRUDs (p. ej. `AdminCrudController`) para impedir editar o eliminar usuarios cuyo rol supera al del usuario actual.

## `App\Security\VirtualPermission`

`src/Security/VirtualPermission.php`. El método `setPermission()` de EasyAdmin acepta una cadena de rol de Symfony. Para **denegar** una acción sin definir un rol real, la base pasa un valor centinela (`NOPERMISSION`) que ningún rol real tendrá jamás:

```php
$actions->setPermissions(array_fill_keys($denied, VirtualPermission::DENY));
$action->setPermission(VirtualPermission::allowed($hasPermissionImpersonate));
```

`VirtualPermission::allowed(bool)` devuelve `''` (sin restricción) o `DENY`. Usa `VirtualPermission::isDenied()` para leer permisos ya adjuntos a un menú o acción.

## Integración en los CRUDs

`App\Controller\Admin\AbstractCrudController::configureActions()` deniega `INDEX/NEW/DETAIL/EDIT/DELETE/BATCH_DELETE` cuando el permiso `crud_*` / `crud_*_<action>` correspondiente no está disponible para el usuario actual. Las subclases amplían esto con reglas más detalladas (p. ej. `AdminCrudController` aplica `isUp()` entre roles de usuario antes de permitir editar/eliminar, y añade una acción `impersonate` controlada por `crud_admin_impersonate`).

## Helpers de Twig

`App\Twig\RolePermissionsExtension` (`src/Twig/RolePermissionsExtension.php`) expone:

```twig
{% if has_permission('media_upload') %} ... {% endif %}
{% if has_permission_crud('user') %} ... {% endif %}
{% if has_permission_crud_action('user', 'edit') %} ... {% endif %}
```

Un token de seguridad que no sea `User` (p. ej. anónimo) se trata como si no tuviera ningún permiso.

## Helper del principal administrador

`App\Security\AdminUserTrait` (`src/Security/AdminUserTrait.php`) expone `user(): User` para los controladores que operan bajo `^/admin`. Lanza `LogicException` cuando el token no es un `App\Entity\User` (uso incorrecto: el firewall garantiza `ROLE_ADMIN` y, por tanto, un `App\Entity\User`).
