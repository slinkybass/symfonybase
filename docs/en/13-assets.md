# Frontend assets

The base uses **Symfony Asset Mapper** plus the **Stimulus** + **Turbo** bridges. There is no Webpack/Encore, no Node bundling.

## Configuration

`config/packages/asset_mapper.yaml`:

```yaml
framework:
    asset_mapper:
        paths:
            - assets/
        missing_import_mode: strict # warn in prod
```

`importmap.php` (project root) declares every JS/CSS dependency and entry point consumed by Twig — consult it before adding a dependency.

## Folder layout

```
assets/
├── controllers/                    # Stimulus controllers
│   └── csrf_protection_controller.js
├── controllers.json                # Bundle controller toggles (UxCollection, UxMedia, Turbo)
├── stimulus_bootstrap.js           # Stimulus auto-import / @symfony/stimulus-bundle bootstrap
├── translator.js                   # ux-translator entry exposed as `trans`
├── images/                         # Default logo, favicon
├── vendor/                         # AssetMapper-tracked vendor downloads
├── css/
│   ├── app.css                     # Loaded from app entrypoint (admin + public)
│   ├── admin.css                   # Loaded from admin entrypoint only
│   └── public.css                  # Loaded from public entrypoint only
└── js/
    ├── app.js                      # Entrypoint registered as `app`
    ├── admin.js                    # Entrypoint registered as `admin`
    ├── public.js                   # Entrypoint registered as `public`
    ├── shell/                      # Entrypoint DOM logic (App / Admin / Public objects)
    │   ├── app.js
    │   ├── admin.js
    │   └── public.js
    ├── base/                       # Platform features (not product JS)
    │   ├── artgris-manager.js
    │   ├── color-scheme.js
    │   └── settings-form.js
    ├── fields/                     # EasyAdmin field plugins
    │   ├── form-type-*.js          # Per-field entrypoints (importmap + App\Field\*)
    │   ├── helpers/                # General form helpers (not form-type-*)
    │   │   ├── autocomplete.js
    │   │   └── hierarchy-fields.js
    │   └── lib/                    # Shared infra for form-type-*
    └── project/                    # Product JS when cloning the base (empty in the starter)
```

**Ownership:** `shell/`, `base/`, and `fields/` belong to the base. When building your app on a clone, put your JS in `project/` (and/or `assets/controllers/` for Stimulus).

## Entry points

`importmap.php` declares the following user-facing entry points (`entrypoint => true`):

| Entry name        | Path                                | Used by                                                         |
| ----------------- | ----------------------------------- | --------------------------------------------------------------- |
| `app`             | `assets/js/app.js`                  | All app pages (admin + public). Adds `App` to `window`.         |
| `admin`           | `assets/js/admin.js`                | Admin pages (loaded in `DashboardController::configureAssets`). |
| `public`          | `assets/js/public.js`               | Public site pages.                                              |
| `color-scheme`    | `assets/js/base/color-scheme.js`    | Imported by `app.js` (light/dark scheme handler).               |
| `artgris-manager` | `assets/js/base/artgris-manager.js` | Loaded by the Artgris file manager Twig override.               |
| `settings-form`   | `assets/js/base/settings-form.js`   | Loaded by `SettingsCrudController::configureAssets`.            |
| `form-type-*`     | `assets/js/fields/form-type-*.js`   | Auto-attached by the corresponding `App\Field\*` wrapper.       |

The fields entries follow the EasyAdmin convention: each `App\Field\*` class registers its plugin with `addAssetMapperEntries(Asset::new('form-type-password')->onlyOnForms())`, so the bundle injects the script only on screens that render the field.

## JavaScript layering

The structure mirrors what `assets/js/app.js`, `assets/js/admin.js` and `assets/js/public.js` do:

- Entry points (`app/admin/public.js`) stay thin: import vendor libs + global CSS, then run `App` / `Admin` / `Public` setup methods inside `DOMContentLoaded`. They expose the page object on `window` so EasyAdmin templates can call into them.
- Modules under `assets/js/shell/` keep the entrypoint DOM logic (named exports such as `App.createAutoCompleteFields`, `Admin.createSearchHighlight`).
- Platform features under `assets/js/base/` are registered as their own importmap entries when they need to be loaded from Twig/CRUD (`artgris-manager`, `settings-form`) or as part of the global shell (`color-scheme`).
- `form-type-*` plugins under `assets/js/fields/` follow the `data-<plugin>-field` pattern and are registered from `App\Field\*`. General helpers live in `fields/helpers/` (e.g. autocomplete, hierarchy-fields) and are imported from the `app` entrypoint, not as per-field importmap entries.
- Product JS for a clone goes in `assets/js/project/` (empty in the starter).

## Stimulus controllers

`assets/controllers.json` toggles bundle-shipped Stimulus controllers (Arkounay UX Collection / Media, Symfony UX Turbo). The single custom controller is `assets/controllers/csrf_protection_controller.js`; `assets/stimulus_bootstrap.js` wires it through `@symfony/stimulus-bundle`.

## CSS

- `app.css` ships the global theme tweaks (Jost font, Tabler / Bootstrap helpers, custom utility classes). Loaded through the `app` entry point.
- `admin.css` is admin-only fine-tuning.
- `public.css` is the place for public-site styles (currently minimal).

EasyAdmin pulls `app` and `admin` via `DashboardController::configureAssets()`; the public site loads `app` and `public` from `templates/public/layout/`.

## Adding a new field plugin

1. Create the JS module under `assets/js/fields/form-type-foo.js`.
2. Register the entry in `importmap.php` (`'form-type-foo' => [...]`).
3. From the corresponding `App\Field\Foo` wrapper, add it to the asset graph: `$this->addAssetMapperEntries(Asset::new('form-type-foo')->onlyOnForms())`.
4. Use a `data-foo-field` HTML attribute to scope the DOM bootstrap.
