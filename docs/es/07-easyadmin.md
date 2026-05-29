# Capa EasyAdmin

El área de administración es una extensión ligera de [EasyAdmin 4](https://symfony.com/bundles/EasyAdminBundle/current/index.html). Las personalizaciones residen en `src/Controller/Admin/` y las sobreescrituras de plantillas en `templates/bundles/EasyAdminBundle/`.

## DashboardController

`src/Controller/Admin/DashboardController.php`. Anotado con `#[AdminDashboard(routePath: '/admin/{_locale}', routeName: 'admin')]`.

Puntos destacados:

- `index()` renderiza `admin/home.html.twig`.
- `configureDashboard()` construye el objeto `Dashboard`: título a partir del logo configurado (`appConfig.appLogo`), ruta del favicon, esquema de color `light` por defecto, contenido renderizado maximizado y selector de idioma construido desde el parámetro `LOCALES` (`config/services.yaml`).
- `configureCrud()` establece la zona horaria global (`appConfig.appTimezone`) y la acción de fila por defecto `DETAIL`.
- `configureAssets()` registra el conjunto de iconos `tabler` y los puntos de entrada `app` y `admin` de Asset Mapper (ver [assets](13-assets.md)).
- `configureMenuItems()` tiene en cuenta los permisos: los permisos reales de aplicación se declaran directamente mediante `App\Security\Permission` + `setPermission()` de EasyAdmin, mientras que la agrupación de menús usa `isGranted()` para que la estructura coincida con el resultado final de acceso.
- `configureUserMenu()` expone el perfil (detalle de `AdminCrudController` para el usuario actual), `Salir de la suplantación` (solo al suplantar) y la entrada de Logout.
- `configureActions()` estandariza los iconos de los botones (Tabler), reordena las acciones y las estiliza (`device-floppy`, `chevron-left`, `trash`, etc.) para todos los CRUDs.

Cuando la fila `Config` aún no existe, los elementos de menú `Settings` y `Config` apuntan a `PAGE_NEW`; una vez creada, apuntan a `PAGE_DETAIL`.

La entrada de menú de la entidad demo opcional es condicional según `class_exists('App\\Entity\\DemoEntity')`.

## AbstractCrudController

`src/Controller/Admin/AbstractCrudController.php`. Clase base para todos los CRUDs en `src/Controller/Admin/Cruds/*`. El constructor inyecta:

- `EntityManagerInterface $em`
- `TranslatorInterface $translator`
- `App\Service\ConfigService $configService`
- `App\Service\RolePermissions $rolePermissions`

Las cuatro son propiedades públicas para que las subclases puedan pasarlas a través de `parent::__construct(...)` y reutilizarlas directamente. `transEntity` tiene por defecto el nombre base del controlador (`UserCrudController` → `user`) y define el prefijo de traducción para las cadenas de la entidad.

### Configuración `Crud` por defecto

`configureCrud()` establece:

- Etiqueta singular que incluye la entidad (`(string) $entity`) cuando está disponible.
- `setDefaultSort(['id' => 'DESC'])`.
- Temas de formulario para Arkounay UX Collection y UX Media.

### Control de permisos

`configureActions()` lee los permisos `crud_<entidad>` y `crud_<entidad>_<acción>` a través de los helpers de permisos / voter y usa [`VirtualPermission::DENY`](05-permissions.md#appsecurityvirtualpermission) solo en el último paso para ocultar acciones en EasyAdmin.

### Helpers disponibles para las subclases

| Helper                                                                                                                  | Propósito                                                               |
| ----------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------- |
| `adminUrl()`                                                                                                            | `AdminUrlGenerator` desde el contenedor                                 |
| `request()`                                                                                                             | `RequestStack` desde el contenedor                                      |
| `session()`                                                                                                             | Sesión actual si está disponible                                        |
| `config()`                                                                                                              | `AppConfig` en caché                                                    |
| `entity()`                                                                                                              | Instancia de entidad activa (contexto EA o cargada por `EA::ENTITY_ID`) |
| `crud()`                                                                                                                | Clave corta de entidad derivada del nombre de la clase controladora     |
| `action()` / `isIndex()` / `isDetail()` / `isNew()` / `isEdit()` / `isForm()`                                           | Atajos para la acción actual de EA                                      |
| `filters()` / `filtersShown()` / `filtersHidden()`                                                                      | Payload de filtros parseado desde `EA::FILTERS`                         |
| `filter($name)` / `filterShown($name)` / `filterHidden($name)`                                                          | Valor de un filtro individual                                           |
| `permission()` / `permissionCrud()` / `permissionCrudAction()`                                                          | Construyen ids de permiso para `isGranted()` / `setPermission()`        |
| `hasPermission()` / `hasPermissionCrud()` / `hasPermissionCrudAction()`                                                 | Wrappers cómodos sobre `isGranted()` para el `User` actual              |
| `transEntitySingular()` / `transEntityPlural()` / `transEntitySection()` / `transEntityAction()` / `transEntityField()` | Búsquedas bajo `entities.{entity}.*`                                    |

`adminUrl()` y `request()` utilizan el patrón de acceso al contenedor heredado de EasyAdmin (`$this->container->get(...)`).

## CRUD controllers

`src/Controller/Admin/Cruds/`:

| Controlador              | Entidad                               | Notas                                                                                              |
| ------------------------ | ------------------------------------- | -------------------------------------------------------------------------------------------------- |
| `UserCrudController`     | `User` (no administrador)             | Filtra roles admin en índice/formularios; `impersonate` con `crud_user_impersonate` + `isUp()`.    |
| `AdminCrudController`    | `User` (administrador)                | `isUp()` en editar/eliminar/suplantar; deniega todas las acciones estándar si falta `crud_admin`.  |
| `RoleCrudController`     | `Role`                                | Switches dinámicos (solo permisos que el editor tiene); jerarquía padre/hijo al guardar.           |
| `SettingsCrudController` | `Config` (campos de marca)            | Fuerza una única fila; redirige al detalle tras guardar; carga la entrada de asset `settingsForm`. |
| `ConfigCrudController`   | `Config` (toggles de funcionalidades) | Mismo comportamiento singleton que Settings.                                                       |

Ambos CRUDs de `User` aplican siempre `IsVerifiedFilter` + `IsAdminFilter` en el `QueryBuilder` del índice para que las cuentas desactivadas/no verificadas y el «lado» incorrecto nunca aparezcan en el listado. También registran un listener de `SUBMIT` en el formulario que hashea `plainPassword` si se ha proporcionado.

`RoleCrudController` recorre `RolePermissions::getGroupedPermissions()` para generar un switch por permiso que el usuario actual puede otorgar, con sangría según el nivel del árbol. En `SUBMIT` reconstruye el mapa `permissions` y anula hijos cuyo padre esté desactivado. Ver [permisos — recetario](05-permissions.md#cómo-aplicar-permisos-recetario).

## Sobreescrituras de plantillas del bundle

`templates/bundles/EasyAdminBundle/`:

- `layout.html.twig` — sobreescritura del layout principal; inyecta variables CSS personalizadas calculadas desde `appConfig.appColor` (usa el filtro Twig `hex_to_rgb`).
- `menu.html.twig`, `flash_messages.html.twig`.
- `crud/` — sobreescrituras de `index`, `detail`, `new`, `edit`, `filters`, `paginator`, `form_theme`, más plantillas por campo bajo `crud/field/`.
- `page/login.html.twig` — usada por `AuthController::login`.
- `components/` — sobreescrituras de `ActionMenu`, `Button`, `Icon`.
- `label/empty.html.twig`.

Ver [templates](12-templates.md) para el árbol completo de plantillas.
