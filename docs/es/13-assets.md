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
    ├── app/                        # Helpers de shell por aplicación (registrados como entradas propias)
    │   ├── artgris_manager.js
    │   ├── page-color-scheme.js
    │   └── settingsForm.js
    ├── page/                       # Módulos de página (objetos App / Admin / Public)
    │   ├── app.js
    │   ├── admin.js
    │   └── public.js
    └── fields/                     # Un módulo por plugin de campo personalizado de EasyAdmin
        ├── autocomplete.js
        ├── form-type-codeeditor.js
        ├── form-type-collection.js
        ├── form-type-color.js
        ├── form-type-date.js
        ├── form-type-datetime.js
        ├── form-type-file.js
        ├── form-type-mask.js
        ├── form-type-password.js
        ├── form-type-signature.js
        ├── form-type-slider.js
        ├── form-type-slug.js
        ├── form-type-textarea.js
        ├── form-type-texteditor.js
        ├── form-type-time.js
        └── hierarchyFields.js
```

## Puntos de entrada

`importmap.php` declara los siguientes puntos de entrada visibles al usuario (`entrypoint => true`):

| Nombre de entrada   | Ruta                                 | Utilizado por                                                                  |
| ------------------- | ------------------------------------ | ------------------------------------------------------------------------------ |
| `app`               | `assets/js/app.js`                   | Todas las páginas (admin + público). Añade `App` a `window`.                   |
| `admin`             | `assets/js/admin.js`                 | Páginas de administración (cargado en `DashboardController::configureAssets`). |
| `public`            | `assets/js/public.js`                | Páginas del sitio público.                                                     |
| `page-color-scheme` | `assets/js/app/page-color-scheme.js` | Importado por `app.js` (gestor de esquema claro/oscuro).                       |
| `artgris_manager`   | `assets/js/app/artgris_manager.js`   | Cargado por `templates/admin/media.html.twig`.                                 |
| `settingsForm`      | `assets/js/app/settingsForm.js`      | Cargado por `SettingsCrudController::configureAssets`.                         |
| `form-type-*`       | `assets/js/fields/form-type-*.js`    | Adjuntado automáticamente por el wrapper `App\Field\*` correspondiente.        |

Las entradas de campos siguen la convención de EasyAdmin: cada clase `App\Field\*` registra su plugin con `addAssetMapperEntries(Asset::new('form-type-password')->onlyOnForms())`, de modo que el bundle inyecta el script únicamente en las pantallas que renderizan el campo.

## Capas de JavaScript

La estructura refleja lo que hacen `assets/js/app.js`, `assets/js/admin.js` y `assets/js/public.js`:

- Los puntos de entrada (`app/admin/public.js`) son ligeros: importan librerías de terceros y CSS global, y ejecutan los métodos de configuración de `App` / `Admin` / `Public` dentro de `DOMContentLoaded`. Exponen el objeto de página en `window` para que las plantillas de EasyAdmin puedan invocarlos.
- Los módulos de página en `assets/js/page/` contienen la lógica DOM (exports con nombre como `App.createAutoCompleteFields`, `Admin.createSearchHighlight`).
- Los helpers de shell por página o funcionalidad en `assets/js/app/` se registran como entradas propias en el importmap cuando deben cargarse desde una plantilla Twig concreta (`artgris_manager`, `settingsForm`) o como parte del shell global de la aplicación (`page-color-scheme`).
- Los plugins de campo en `assets/js/fields/` siguen el patrón de atributo `data-<plugin>-field` (p. ej. `data-signature-field`, `data-codeeditor-field`, `data-slug-field`) para localizar nodos del DOM e inicializar la librería correspondiente.

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
