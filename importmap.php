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
];
