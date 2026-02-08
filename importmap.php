<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    'sortablejs' => [
        'version' => '1.15.6',
    ],
    'cropperjs' => [
        'version' => '1.6.2',
    ],
    'cropperjs/dist/cropper.min.css' => [
        'version' => '1.6.2',
        'type' => 'css',
    ],
    'bootstrap' => [
        'version' => '5.3.8',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.8',
        'type' => 'css',
    ],
    'intl-messageformat' => [
        'version' => '10.7.18',
    ],
    'tslib' => [
        'version' => '2.8.1',
    ],
    '@formatjs/fast-memoize' => [
        'version' => '2.2.7',
    ],
    '@formatjs/icu-messageformat-parser' => [
        'version' => '2.11.4',
    ],
    '@formatjs/icu-skeleton-parser' => [
        'version' => '1.8.16',
    ],
    '@symfony/ux-translator' => [
        'path' => './vendor/symfony/ux-translator/assets/dist/translator_controller.js',
    ],
    '@tabler/core' => [
        'version' => '1.4.0',
    ],
    '@tabler/core/dist/css/tabler.min.css' => [
        'version' => '1.4.0',
        'type' => 'css',
    ],
    '@tabler/core/dist/css/tabler-flags.min.css' => [
        'version' => '1.4.0',
        'type' => 'css',
    ],
    '@tabler/core/dist/css/tabler-vendors.min.css' => [
        'version' => '1.4.0',
        'type' => 'css',
    ],
    'mark.js' => [
        'version' => '8.11.1',
    ],
    'dirty-form' => [
        'version' => '1.0.0',
    ],
    'sweetalert2' => [
        'version' => '11.26.17',
    ],
    'sweetalert2/dist/sweetalert2.min.css' => [
        'version' => '11.26.17',
        'type' => 'css',
    ],
    'moment/min/moment-with-locales.min.js' => [
        'version' => '2.30.1',
    ],
    'tom-select' => [
        'version' => '2.4.3',
    ],
    '@orchidjs/sifter' => [
        'version' => '1.1.0',
    ],
    '@orchidjs/unicode-variants' => [
        'version' => '1.1.2',
    ],
    'tom-select/dist/css/tom-select.bootstrap5.css' => [
        'version' => '2.4.3',
        'type' => 'css',
    ],
    'flatpickr' => [
        'version' => '4.6.13',
    ],
    'flatpickr/dist/flatpickr.min.css' => [
        'version' => '4.6.13',
        'type' => 'css',
    ],
    'flatpickr/dist/l10n/index.js' => [
        'version' => '4.6.13',
    ],
    'slugify' => [
        'version' => '1.6.6',
    ],
    'tinymce' => [
        'version' => '8.3.1',
    ],
    'tinymce/models/dom/model' => [
        'version' => '8.3.1',
    ],
    'tinymce/themes/silver' => [
        'version' => '8.3.1',
    ],
    'tinymce/icons/default' => [
        'version' => '8.3.1',
    ],
    'tinymce/skins/ui/oxide/skin.min.css' => [
        'version' => '8.3.1',
        'type' => 'css',
    ],
    'tinymce/plugins/accordion' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/advlist' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/anchor' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/autolink' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/autoresize' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/autosave' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/charmap' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/code' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/codesample' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/directionality' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/emoticons' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/emoticons/js/emojis' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/fullscreen' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/image' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/importcss' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/insertdatetime' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/link' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/lists' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/media' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/nonbreaking' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/pagebreak' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/preview' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/quickbars' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/save' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/searchreplace' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/table' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/visualblocks' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/visualchars' => [
        'version' => '8.3.1',
    ],
    'tinymce/plugins/wordcount' => [
        'version' => '8.3.1',
    ],
    'tinymce-i18n/langs/es.js' => [
        'version' => '25.11.17',
    ],
    'ace-builds/src-min-noconflict/ace' => [
        'version' => '1.43.5',
    ],
    'spectrum-vanilla' => [
        'version' => '1.1.1',
    ],
    'spectrum-vanilla/dist/spectrum.min.css' => [
        'version' => '1.1.1',
        'type' => 'css',
    ],
    'signature_pad' => [
        'version' => '5.1.3',
    ],
    'basiclightbox' => [
        'version' => '5.0.4',
    ],
    'basiclightbox/dist/basicLightbox.min.css' => [
        'version' => '5.0.4',
        'type' => 'css',
    ],
    'nouislider' => [
        'version' => '15.8.1',
    ],
    'nouislider/dist/nouislider.min.css' => [
        'version' => '15.8.1',
        'type' => 'css',
    ],
    'imask' => [
        'version' => '7.6.1',
    ],
    'app' => [
        'path' => './assets/js/app.js',
        'entrypoint' => true,
    ],
    'admin' => [
        'path' => './assets/js/admin.js',
        'entrypoint' => true,
    ],
    'public' => [
        'path' => './assets/js/public.js',
        'entrypoint' => true,
    ],
    'page-color-scheme' => [
        'path' => './assets/js/app/page-color-scheme.js',
        'entrypoint' => true,
    ],
    'artgris_manager' => [
        'path' => './assets/js/app/artgris_manager.js',
        'entrypoint' => true,
    ],
    'settingsForm' => [
        'path' => './assets/js/app/settingsForm.js',
        'entrypoint' => true,
    ],
    'form-type-codeeditor' => [
        'path' => './assets/js/fields/form-type-codeeditor.js',
        'entrypoint' => true,
    ],
    'form-type-collection' => [
        'path' => './assets/js/fields/form-type-collection.js',
        'entrypoint' => true,
    ],
    'form-type-color' => [
        'path' => './assets/js/fields/form-type-color.js',
        'entrypoint' => true,
    ],
    'form-type-date' => [
        'path' => './assets/js/fields/form-type-date.js',
        'entrypoint' => true,
    ],
    'form-type-datetime' => [
        'path' => './assets/js/fields/form-type-datetime.js',
        'entrypoint' => true,
    ],
    'form-type-file' => [
        'path' => './assets/js/fields/form-type-file.js',
        'entrypoint' => true,
    ],
    'form-type-mask' => [
        'path' => './assets/js/fields/form-type-mask.js',
        'entrypoint' => true,
    ],
    'form-type-password' => [
        'path' => './assets/js/fields/form-type-password.js',
        'entrypoint' => true,
    ],
    'form-type-signature' => [
        'path' => './assets/js/fields/form-type-signature.js',
        'entrypoint' => true,
    ],
    'form-type-slider' => [
        'path' => './assets/js/fields/form-type-slider.js',
        'entrypoint' => true,
    ],
    'form-type-slug' => [
        'path' => './assets/js/fields/form-type-slug.js',
        'entrypoint' => true,
    ],
    'form-type-textarea' => [
        'path' => './assets/js/fields/form-type-textarea.js',
        'entrypoint' => true,
    ],
    'form-type-texteditor' => [
        'path' => './assets/js/fields/form-type-texteditor.js',
        'entrypoint' => true,
    ],
    'form-type-time' => [
        'path' => './assets/js/fields/form-type-time.js',
        'entrypoint' => true,
    ],
];
