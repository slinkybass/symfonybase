# Assets de frontend

La base utiliza **Symfony Asset Mapper** junto con los bridges de **Stimulus** y **Turbo**. No hay Webpack/Encore ni empaquetado con Node.

## Configuración

`config/packages/asset_mapper.yaml`:

```yaml
framework:
    asset_mapper:
        paths:
            - assets/
        missing_import_mode: strict # warn in prod
```

`importmap.php` (en la raíz del proyecto) declara todas las dependencias JS/CSS y los puntos de entrada consumidos por Twig — consúltalo antes de añadir una dependencia.

## Estructura de carpetas

```
assets/
├── controllers/                    # Controladores Stimulus
│   └── csrf_protection_controller.js
├── controllers.json                # Activación de controladores de bundle (UxCollection, UxMedia, Turbo)
├── stimulus_bootstrap.js           # Auto-import de Stimulus / bootstrap de @symfony/stimulus-bundle
├── translator.js                   # Entrada ux-translator expuesta como `trans`
├── images/                         # Logo y favicon por defecto
├── vendor/                         # Descargas de terceros gestionadas por AssetMapper
├── css/
│   ├── app.css                     # Cargado desde el entrypoint app (admin + público)
│   ├── admin.css                   # Cargado solo desde el entrypoint admin
│   └── public.css                  # Cargado solo desde el entrypoint public
└── js/
    ├── app.js                      # Entrypoint registrado como `app`
    ├── admin.js                    # Entrypoint registrado como `admin`
    ├── public.js                   # Entrypoint registrado como `public`
    ├── shell/                      # Lógica DOM de los entrypoints (objetos App / Admin / Public)
    │   ├── app.js
    │   ├── admin.js
    │   └── public.js
    ├── base/                       # Features de la plataforma (no JS de producto)
    │   ├── artgris-manager.js
    │   ├── color-scheme.js
    │   └── settings-form.js
    ├── fields/                     # Plugins de campo EasyAdmin
    │   ├── form-type-*.js          # Entrypoints por campo (importmap + App\Field\*)
    │   ├── helpers/                # Helpers generales de formulario (no form-type-*)
    │   │   ├── autocomplete.js
    │   │   └── hierarchy-fields.js
    │   └── lib/                    # Infra compartida de los form-type-*
    └── project/                    # JS del producto al clonar la base (vacío en el starter)
```

**Ownership:** `shell/`, `base/` y `fields/` pertenecen a la base. Al construir tu app sobre el clon, mete tu JS en `project/` (y/o `assets/controllers/` para Stimulus).

## Puntos de entrada

`importmap.php` declara los siguientes puntos de entrada visibles al usuario (`entrypoint => true`):

| Nombre de entrada | Ruta                                 | Utilizado por                                                                  |
| ----------------- | ------------------------------------ | ------------------------------------------------------------------------------ |
| `app`             | `assets/js/app.js`                   | Todas las páginas (admin + público). Añade `App` a `window`.                   |
| `admin`           | `assets/js/admin.js`                 | Páginas de administración (cargado en `DashboardController::configureAssets`). |
| `public`          | `assets/js/public.js`                | Páginas del sitio público.                                                     |
| `color-scheme`    | `assets/js/base/color-scheme.js`     | Importado por `app.js` (gestor de esquema claro/oscuro).                       |
| `artgris-manager` | `assets/js/base/artgris-manager.js`  | Cargado por el override del file manager Artgris.                              |
| `settings-form`   | `assets/js/base/settings-form.js`    | Cargado por `SettingsCrudController::configureAssets`.                         |
| `form-type-*`     | `assets/js/fields/form-type-*.js`    | Adjuntado automáticamente por el wrapper `App\Field\*` correspondiente.        |

Las entradas de campos siguen la convención de EasyAdmin: cada clase `App\Field\*` registra su plugin con `addAssetMapperEntries(Asset::new('form-type-password')->onlyOnForms())`, de modo que el bundle inyecta el script únicamente en las pantallas que renderizan el campo.

## Capas de JavaScript

La estructura refleja lo que hacen `assets/js/app.js`, `assets/js/admin.js` y `assets/js/public.js`:

- Los puntos de entrada (`app/admin/public.js`) son ligeros: importan librerías de terceros y CSS global, y ejecutan los métodos de configuración de `App` / `Admin` / `Public` dentro de `DOMContentLoaded`. Exponen el objeto de página en `window` para que las plantillas de EasyAdmin puedan invocarlos.
- Los módulos en `assets/js/shell/` contienen la lógica DOM del entrypoint (exports con nombre como `App.createAutoCompleteFields`, `Admin.createSearchHighlight`).
- Las features de plataforma en `assets/js/base/` se registran como entradas propias en el importmap cuando deben cargarse desde Twig/CRUD (`artgris-manager`, `settings-form`) o como parte del shell global (`color-scheme`).
- Los plugins `form-type-*` en `assets/js/fields/` siguen el patrón `data-<plugin>-field` y se registran desde `App\Field\*`. Los helpers generales viven en `fields/helpers/` (p. ej. autocomplete, hierarchy-fields) y se importan desde el entrypoint `app`, no vía importmap por campo.
- El JS de producto del clon va en `assets/js/project/` (vacío en el starter).

## Controladores Stimulus

`assets/controllers.json` activa o desactiva los controladores Stimulus incluidos en los bundles (Arkounay UX Collection / Media, Symfony UX Turbo). El único controlador personalizado es `assets/controllers/csrf_protection_controller.js`; `assets/stimulus_bootstrap.js` lo registra a través de `@symfony/stimulus-bundle`.

## CSS

- `app.css` incluye los ajustes globales del tema (fuente Jost, helpers de Tabler / Bootstrap, clases de utilidad personalizadas). Se carga a través del entrypoint `app`.
- `admin.css` contiene ajustes exclusivos del panel de administración.
- `public.css` es el lugar para los estilos del sitio público (actualmente mínimo).

EasyAdmin carga `app` y `admin` mediante `DashboardController::configureAssets()`; el sitio público carga `app` y `public` desde `templates/public/layout/`.

## Añadir un nuevo plugin de campo

1. Crea el módulo JS en `assets/js/fields/form-type-foo.js`.
2. Registra la entrada en `importmap.php` (`'form-type-foo' => [...]`).
3. Desde el wrapper `App\Field\Foo` correspondiente, añádela al grafo de assets: `$this->addAssetMapperEntries(Asset::new('form-type-foo')->onlyOnForms())`.
4. Usa el atributo HTML `data-foo-field` para delimitar el bootstrap del DOM.
