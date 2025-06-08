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
    '@tabler/core' => [
        'version' => '1.2.0',
    ],
    '@tabler/core/dist/css/tabler.min.css' => [
        'version' => '1.2.0',
        'type' => 'css',
    ],
    '@tabler/core/dist/css/tabler-flags.min.css' => [
        'version' => '1.2.0',
        'type' => 'css',
    ],
    '@tabler/core/dist/css/tabler-vendors.min.css' => [
        'version' => '1.2.0',
        'type' => 'css',
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
    'form-type-date' => [
        'path' => './assets/js/fields/form-type-date.js',
        'entrypoint' => true,
    ],
    'form-type-datetime' => [
        'path' => './assets/js/fields/form-type-datetime.js',
        'entrypoint' => true,
    ],
    'form-type-password' => [
        'path' => './assets/js/fields/form-type-password.js',
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
    'form-type-time' => [
        'path' => './assets/js/fields/form-type-time.js',
        'entrypoint' => true,
    ],
    'mark.js' => [
        'version' => '8.11.1',
    ],
    'dirty-form' => [
        'version' => '0.4.0',
    ],
    'sweetalert2' => [
        'version' => '11.22.0',
    ],
    'sweetalert2/dist/sweetalert2.min.css' => [
        'version' => '11.22.0',
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
];
