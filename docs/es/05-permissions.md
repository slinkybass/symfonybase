# Permisos

La base incluye su propio sistema de permisos sobre los roles de Symfony.

**Dos capas (no las mezcles):**

| Capa               | Mecanismo                                                          | Qué controla                                    |
| ------------------ | ------------------------------------------------------------------ | ----------------------------------------------- |
| Firewall           | `ROLE_ADMIN` desde `Role::isAdmin` + `access_control` en `^/admin` | Entrar o no en el área de administración        |
| Mapa de aplicación | JSON `permissions` en `Role`, vía `PermissionVoter`                | Menús, acciones CRUD, rutas admin propias, Twig |

Los roles de Symfony (`ROLE_USER`, `ROLE_ADMIN`, …) siguen controlando el firewall. El mapa de permisos decide **qué puede ver y hacer cada rol dentro de EasyAdmin** (por CRUD, por acción) y en otras rutas del admin.

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

Los ids de permisos se almacenan y se usan de extremo a extremo con guiones bajos (`crud_user`, `media_upload`).

| Patrón                        | Ejemplo                 | Origen                                                 |
| ----------------------------- | ----------------------- | ------------------------------------------------------ |
| `crud_<entity>`               | `crud_user`             | Existencia de `UserCrudController.php`                 |
| `crud_<entity>_<action>`      | `crud_user_edit`        | Generado automáticamente para `new/detail/edit/delete` |
| `crud_<entity>_<extraAction>` | `crud_user_impersonate` | `Permission::EXTRA_CRUD_ACTIONS`                       |
| `<custom>`                    | `media_upload`          | `Permission::EXTRA_PERMISSIONS`                        |

Los identificadores se normalizan dividiendo por `_`, poniendo en minúscula la primera letra de cada segmento y volviendo a unir: `DemoEntity` → `demoEntity`, `config_Edit` → `config_edit`. Esto preserva las entidades camelCase de varias palabras.

### Registro (un solo sitio)

Toda la declaración de permisos del starter vive en `src/Security/Permission.php`:

- `Permission::EXTRA_PERMISSIONS` — permisos no-CRUD (`media`, `media_upload`, …).
- `Permission::EXTRA_CRUD_ACTIONS` — acciones CRUD personalizadas (`admin => impersonate`, …).
- `Permission::DISABLED_CRUD_ACTIONS` — acciones estándar a excluir por entidad (`config => new/edit/…`, `settings => …`).

`RolePermissions` solo escanea CrudControllers y aplica este registro; no define nombres de permisos.

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

`isUp()` compara dos roles como mapas planos: el rol **A** está “por encima” de **B** cuando cada permiso activo en **B** también lo está en **A**. Se usa en CRUDs (`AdminCrudController`, `RoleCrudController`, acciones de suplantación, …) para impedir actuar sobre usuarios o roles que superan al actual.

Al guardar un rol en `RoleCrudController`, un segundo recorrido del árbol fuerza a `false` cualquier hijo cuyo padre esté desactivado (en servidor, independiente de los toggles jerárquicos del navegador).

## `App\Security\Permission`

`src/Security/Permission.php`. Pequeño constructor de ids de permiso para `isGranted()` y `setPermission()` de EasyAdmin. Evita recordar el formato de cadenas y refleja exactamente los ids persistidos en los roles.

```php
Permission::MEDIA
Permission::ACTION_IMPERSONATE
Permission::crud('user')                              // crud_user
Permission::crudAction('admin', Permission::ACTION_IMPERSONATE)  // crud_admin_impersonate
```

Usa constantes o `crud()` / `crudAction()` desde controladores y menús. Registra permisos nuevos solo en `Permission.php` (`EXTRA_*`, `DISABLED_CRUD_ACTIONS`, constantes opcionales).

## `App\Security\Voter\PermissionVoter`

`src/Security/Voter/PermissionVoter.php`. Adapta las comprobaciones de autorización de Symfony a `RolePermissions::userHasPermission()`. Cualquier llamada soportada a `isGranted()`, `#[IsGranted(...)]` o `setPermission()` de EasyAdmin pasa por este voter y se compara contra los ids persistidos.

## `App\Security\VirtualPermission`

`src/Security/VirtualPermission.php`. El método `setPermission()` de EasyAdmin acepta una cadena de rol/atributo de Symfony. Para **denegar** u ocultar algo según un booleano simple o una regla contextual (no un permiso persistido), la base pasa un valor centinela (`NOPERMISSION`) que ningún rol real tendrá jamás:

```php
$actions->setPermissions(array_fill_keys($denied, VirtualPermission::DENY));
$field->setPermission(VirtualPermission::allowed($visible));
```

`VirtualPermission::allowed(bool)` devuelve `''` (sin restricción) o `DENY`. Resérvalo para booleanos de UI como `displayIf(bool)` o para denegaciones contextuales que no se puedan representar como un permiso reutilizable de aplicación.

## Integración en los CRUDs

`App\Controller\Admin\AbstractCrudController` expone los helpers `permission()`, `permissionCrud()` y `permissionCrudAction()` para construir ids de permiso, además de los wrappers `hasPermission*()` sobre `isGranted()`. `configureActions()` deniega `INDEX/NEW/DETAIL/EDIT/DELETE/BATCH_DELETE` cuando falta el `crud_*` / `crud_*_<action>` correspondiente. Si falta el propio `crud_<entidad>`, se deniegan **todas** las acciones estándar (los CRUD personalizados como `AdminCrudController` deben conservar esta regla al sobrescribir `configureActions()`).

Las subclases añaden reglas: `AdminCrudController` / `UserCrudController` usan `isUp()` en editar/eliminar/suplantar; `RoleCrudController` solo muestra switches de permisos que el editor ya tiene y bloquea editar el propio rol.

## Helpers de Twig

`App\Twig\RolePermissionsExtension` (`src/Twig/RolePermissionsExtension.php`) expone:

```twig
{% if has_permission('media_upload') %} ... {% endif %}
{% if has_permission_crud('user') %} ... {% endif %}
{% if has_permission_crud_action('user', 'edit') %} ... {% endif %}
```

Un token de seguridad que no sea `User` (p. ej. anónimo) se trata como si no tuviera ningún permiso. En Twig también puedes usar `is_granted('media')` para el usuario **actual** — pasa por el mismo `PermissionVoter`. Usa `has_permission(..., otroUsuario)` cuando necesites comprobar el rol de otra persona (poco habitual).

## Cómo aplicar permisos (recetario)

Registra primero cada id nuevo en `Permission.php` (`EXTRA_PERMISSIONS`, `EXTRA_CRUD_ACTIONS` y/o un `*CrudController.php`). Luego ejecuta `php bin/console app:update-permissions` para que `ROLE_SUPERADMIN` reciba las claves (ver [consola](16-console.md)).

En PHP, prioriza **constantes de `Permission` / `crud()` / `crudAction()`** y **`isGranted()`** para que los ids queden normalizados y el voter siga siendo la única puerta.

### Rutas admin propias (fuera del CRUD de EasyAdmin)

`src/Controller/AdminController.php` — atributo en la acción:

```php
use App\Security\Permission;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminRoute('/media', name: 'media')]
#[IsGranted(Permission::MEDIA)]
public function media(): void
{
}
```

Equivalente en un método: `$this->denyAccessUnlessGranted(Permission::MEDIA);`

### Menú lateral de EasyAdmin

`DashboardController::configureMenuItems()` — pasa el id al ítem; usa `isGranted()` solo para lógica de agrupación (p. ej. submenús):

```php
$permissionMedia = Permission::MEDIA;

yield MenuItem::linkToRoute('…', 'file', 'admin_media')
    ->setPermission($permissionMedia);

if ($this->isGranted($permissionMedia)) { … }
```

Los enlaces a CRUD usan `Permission::crud('user')` igual (`setPermission($permissionUser)`).

### Acciones CRUD estándar (new / edit / delete / index)

Las hereda `AbstractCrudController::configureActions()` cuando la subclase llama a `parent::configureActions($actions)` (p. ej. `UserCrudController`, `RoleCrudController`). No hace falta código extra salvo excepciones.

Relación:

- `crud_<entidad>` → listado (`INDEX`) y acceso base.
- `crud_<entidad>_new`, `_detail`, `_edit`, `_delete` → cada acción (`BATCH_DELETE` sigue a delete).

### Acciones CRUD personalizadas (p. ej. suplantar)

1. Añade la acción en `Permission::EXTRA_CRUD_ACTIONS` para la entidad (`admin`, `user`, …).
2. Ejecuta `app:update-permissions`.
3. Enlázala en EasyAdmin:

```php
$impersonate = Action::new('impersonate', '…')
    ->linkToUrl(fn (User $u) => $this->generateUrl('home', ['_switch_user' => $u->getEmail()]))
    ->displayIf(fn (User $u) => $user !== $u
        && $u->isActive()
        && $this->rolePermissions->isUp($user->getRole(), $u->getRole()));

$actions->add(Crud::PAGE_INDEX, $impersonate);
$actions->setPermission('impersonate', $this->permissionCrudAction(Permission::ACTION_IMPERSONATE));
```

`setPermission()` aplica el permiso persistido; `displayIf()` añade reglas contextuales (`isUp`, no ser tú mismo, etc.).

### Sobrescribir `configureActions()` en una subclase

Llama a `parent::configureActions($actions)` cuando puedas. Si reemplazas la lista de denegados (p. ej. `AdminCrudController`), con `!$this->hasPermissionCrud()` deniega al menos `INDEX`, `NEW`, `DETAIL`, `EDIT`, `DELETE` y `BATCH_DELETE` — no solo `INDEX`.

Ocultar acciones sin permiso persistido:

```php
$actions->setPermissions(array_fill_keys($denied, VirtualPermission::DENY));
```

### Campos y UI condicional en CRUDs

`FieldGenerator` / `FieldTrait::displayIf(bool)` usa `VirtualPermission::allowed($visible)` — flags de configuración, no permisos de rol:

```php
$field->displayIf($this->config()->enablePublic);
```

Para un permiso de **rol** en un campo, usa `setPermission($this->permissionCrudAction(Action::EDIT))` en el DTO del campo si hace falta.

### Comprobaciones dentro de un CRUD o servicio

```php
// En cualquier subclase de AbstractCrudController
if ($this->hasPermissionCrud()) { … }
if ($this->hasPermissionCrudAction(Action::EDIT)) { … }
if ($this->hasPermission(Permission::MEDIA_UPLOAD)) { … }

// RolePermissions inyectado (p. ej. controlador propio)
$this->rolePermissions->userHasPermission($user, Permission::MEDIA);
$this->rolePermissions->isUp($rolEditor, $rolObjetivo);
```

### Plantillas Twig

Usuario actual (equivalente a `isGranted`):

```twig
{% if is_granted(constant('App\\Security\\Permission::MEDIA')) %}…{% endif %}
{% if has_permission_crud('user') %}…{% endif %}
```

Otro usuario:

```twig
{% if has_permission('media_upload', algunUsuario) %}…{% endif %}
```

### Para qué no usar permisos de aplicación

- **Solo firewall** — `ROLE_ADMIN`, `IS_AUTHENTICATED`, `IS_IMPERSONATOR` en `security.yaml` / `AccessSubscriber`.
- **Booleanos puntuales** — `VirtualPermission::allowed()` o `VirtualPermission::DENY`, no una clave JSON nueva.
- **Ocultar sin reforzar** — en acciones sensibles combina `displayIf()` con `setPermission()` cuando exista permiso persistido.

## Helper del principal administrador

`App\Security\AdminUserTrait` (`src/Security/AdminUserTrait.php`) expone `user(): User` para los controladores que operan bajo `^/admin`. Lanza `LogicException` cuando el token no es un `App\Entity\User` (uso incorrecto: el firewall garantiza `ROLE_ADMIN` y, por tanto, un `App\Entity\User`).
