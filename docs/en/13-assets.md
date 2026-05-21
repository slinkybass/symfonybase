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

`importmap.php` (project root) declares every JS/CSS dependency and the entry points consumed by Twig. The full list of vendor packages — Bootstrap 5, Tabler, TomSelect, Flatpickr, TinyMCE, Ace, SignaturePad, Cropper.js, noUiSlider, IMask, SweetAlert2, Mark.js, Moment with locales, DirtyForm, basicLightbox, Slugify, Sortable, Spectrum Vanilla, etc. — is the source of truth; consult it before adding a dependency.

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
    ├── app/                        # Per-app shell helpers (registered as their own entries)
    │   ├── artgris_manager.js
    │   ├── page-color-scheme.js
    │   └── settingsForm.js
    ├── page/                       # Page modules (App / Admin / Public objects)
    │   ├── app.js
    │   ├── admin.js
    │   └── public.js
    └── fields/                     # One module per custom EasyAdmin field plugin
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

## Entry points

`importmap.php` declares the following user-facing entry points (`entrypoint => true`):

| Entry name          | Path                                 | Used by                                                         |
| ------------------- | ------------------------------------ | --------------------------------------------------------------- |
| `app`               | `assets/js/app.js`                   | All app pages (admin + public). Adds `App` to `window`.         |
| `admin`             | `assets/js/admin.js`                 | Admin pages (loaded in `DashboardController::configureAssets`). |
| `public`            | `assets/js/public.js`                | Public site pages.                                              |
| `page-color-scheme` | `assets/js/app/page-color-scheme.js` | Imported by `app.js` (light/dark scheme handler).               |
| `artgris_manager`   | `assets/js/app/artgris_manager.js`   | Loaded by `templates/admin/media.html.twig`.                    |
| `settingsForm`      | `assets/js/app/settingsForm.js`      | Loaded by `SettingsCrudController::configureAssets`.            |
| `form-type-*`       | `assets/js/fields/form-type-*.js`    | Auto-attached by the corresponding `App\Field\*` wrapper.       |

The fields entries follow the EasyAdmin convention: each `App\Field\*` class registers its plugin with `addAssetMapperEntries(Asset::new('form-type-password')->onlyOnForms())`, so the bundle injects the script only on screens that render the field.

## JavaScript layering

The structure mirrors what `assets/js/app.js`, `assets/js/admin.js` and `assets/js/public.js` do:

- Entry points (`app/admin/public.js`) stay thin: import vendor libs + global CSS, then run `App` / `Admin` / `Public` setup methods inside `DOMContentLoaded`. They expose the page object on `window` so EasyAdmin templates can call into them.
- Page modules under `assets/js/page/` keep the DOM logic (named exports such as `App.createAutoCompleteFields`, `Admin.createSearchHighlight`).
- Per-page or per-feature shell helpers under `assets/js/app/` are registered as their own importmap entries when they need to be loaded from a specific Twig template (`artgris_manager`, `settingsForm`) or as part of the global app shell (`page-color-scheme`).
- Field plugins under `assets/js/fields/` follow the `data-<plugin>-field` attribute pattern (e.g. `data-signature-field`, `data-codeeditor-field`, `data-slug-field`) to find DOM nodes and initialize the corresponding library.

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
