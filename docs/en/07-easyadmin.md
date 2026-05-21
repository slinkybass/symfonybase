# EasyAdmin layer

The admin area is a thin extension of [EasyAdmin 4](https://symfony.com/bundles/EasyAdminBundle/current/index.html). The customizations live in `src/Controller/Admin/` and template overrides in `templates/bundles/EasyAdminBundle/`.

## DashboardController

`src/Controller/Admin/DashboardController.php`. Annotated `#[AdminDashboard(routePath: '/admin/{_locale}', routeName: 'admin')]`.

Highlights:

- `index()` renders `admin/home.html.twig`.
- `configureDashboard()` builds the `Dashboard` object: title from the configured logo (`appConfig.appLogo`), favicon path, default `light` color scheme, content rendered maximized, and locale switcher built from the `LOCALES` parameter (`config/services.yaml`).
- `configureCrud()` sets the global timezone (`appConfig.appTimezone`) and default row action `DETAIL`.
- `configureAssets()` registers the `tabler` icon set and the `app` and `admin` Asset Mapper entry points (see [assets](13-assets.md)).
- `configureMenuItems()` is permission-aware: every menu item declares its `crud_*` permission via `VirtualPermission::allowed(...)`. Items collapse into a sub-menu only when more than one entry of the group is allowed.
- `configureUserMenu()` exposes the profile (`AdminCrudController` detail for the current user), `Exit impersonation` (only when impersonating), and Logout entries.
- `configureActions()` standardizes button icons (Tabler), reorders actions, and styles them (`device-floppy`, `chevron-left`, `trash`, etc.) for all CRUDs.

When a `Config` row does not yet exist, `Settings` and `Config` menu items fall back to `PAGE_NEW`; once the row exists they point to `PAGE_DETAIL`.

The optional demo entity menu entry is conditional on `class_exists('App\\Entity\\DemoEntity')`.

## AbstractCrudController

`src/Controller/Admin/AbstractCrudController.php`. Base class for every CRUD in `src/Controller/Admin/Cruds/*`. Constructor injects:

- `EntityManagerInterface $em`
- `TranslatorInterface $translator`
- `App\Service\ConfigService $configService`
- `App\Service\RolePermissions $rolePermissions`

All four are public properties so subclasses can pass them through `parent::__construct(...)` and reuse them directly. `transEntity` defaults to the basename of the controller (`UserCrudController` → `user`) and drives the translation prefix for entity strings.

### Default `Crud` configuration

`configureCrud()` sets:

- Singular label that includes the entity (`(string) $entity`) when available.
- `setDefaultSort(['id' => 'DESC'])`.
- Form themes for Arkounay UX Collection and UX Media.

### Permission gating

`configureActions()` reads the `crud_<entity>` and `crud_<entity>_<action>` permissions and uses [`VirtualPermission::DENY`](05-permissions.md#appsecurityvirtualpermission) to hide unauthorized actions.

### Helpers exposed to subclasses

| Helper                                                                                                                  | Purpose                                                          |
| ----------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------- |
| `adminUrl()`                                                                                                            | `AdminUrlGenerator` from the container                           |
| `request()`                                                                                                             | `RequestStack` from the container                                |
| `session()`                                                                                                             | Current session if available                                     |
| `config()`                                                                                                              | Cached `AppConfig`                                               |
| `entity()`                                                                                                              | Active entity instance (EA context or loaded by `EA::ENTITY_ID`) |
| `crud()`                                                                                                                | Short entity key derived from controller class name              |
| `action()` / `isIndex()` / `isDetail()` / `isNew()` / `isEdit()` / `isForm()`                                           | Current EA action shortcuts                                      |
| `filters()` / `filtersShown()` / `filtersHidden()`                                                                      | Parsed `EA::FILTERS` query payload                               |
| `filter($name)` / `filterShown($name)` / `filterHidden($name)`                                                          | Single filter value                                              |
| `hasPermission()` / `hasPermissionCrud()` / `hasPermissionCrudAction()`                                                 | Convenience over `RolePermissions` for the current `User`        |
| `transEntitySingular()` / `transEntityPlural()` / `transEntitySection()` / `transEntityAction()` / `transEntityField()` | Lookups under `entities.{entity}.*`                              |

`adminUrl()` and `request()` use the legacy container access pattern (`$this->container->get(...)`) inherited from EasyAdmin.

## CRUD controllers

`src/Controller/Admin/Cruds/`:

| Controller               | Entity                     | Notes                                                                                     |
| ------------------------ | -------------------------- | ----------------------------------------------------------------------------------------- |
| `UserCrudController`     | `User` (non-admin)         | Filters out admin roles from index/forms; impersonate action via `crud_user_impersonate`. |
| `AdminCrudController`    | `User` (admin)             | Adds role-up checks (`RolePermissions::isUp`) on edit/delete; impersonate action.         |
| `RoleCrudController`     | `Role`                     | Renders the dynamic permission tree as nested switches; per-row admins/users links.       |
| `SettingsCrudController` | `Config` (branding fields) | Forces a single row; redirects to detail after save; loads `settingsForm` asset entry.    |
| `ConfigCrudController`   | `Config` (feature toggles) | Same singleton behavior as Settings.                                                      |

Both `User` CRUDs always apply `IsVerifiedFilter` + `IsAdminFilter` on the index `QueryBuilder` so deactivated/unverified accounts and the wrong "side" never leak into the listing. They also wire a Form `SUBMIT` listener that hashes `plainPassword` if provided.

`RoleCrudController` walks `RolePermissions::getGroupedPermissions()` to generate one switch per available permission, indented by tree level. On `SUBMIT` it rebuilds the role's `permissions` map from the form values.

## Bundle template overrides

`templates/bundles/EasyAdminBundle/`:

- `layout.html.twig` — main layout override; injects custom CSS variables computed from `appConfig.appColor` (uses the `hex_to_rgb` Twig filter).
- `menu.html.twig`, `flash_messages.html.twig`.
- `crud/` — `index`, `detail`, `new`, `edit`, `filters`, `paginator`, `form_theme` overrides, plus per-field templates under `crud/field/`.
- `page/login.html.twig` — used by `AuthController::login`.
- `components/` — overrides for `ActionMenu`, `Button`, `Icon`.
- `label/empty.html.twig`.

See [templates](12-templates.md) for the full template tree.
