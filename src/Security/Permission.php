<?php

namespace App\Security;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

/**
 * Single registry for application permission ids and CRUD permission rules.
 *
 * Register extras in {@see EXTRA_PERMISSIONS}, custom CRUD actions in
 * {@see EXTRA_CRUD_ACTIONS}, and excluded standard actions in {@see DISABLED_CRUD_ACTIONS}.
 * Reference ids via constants or {@see crud()} / {@see crudAction()}.
 */
final class Permission
{
    public const CRUD = 'crud';

    public const ACTION_IMPERSONATE = 'impersonate';

    public const MEDIA = 'media';
    public const MEDIA_TREE = 'media_tree';
    public const MEDIA_UPLOAD = 'media_upload';
    public const MEDIA_EDIT = 'media_edit';
    public const MEDIA_FOLDERS = 'media_folders';

    /** @var list<string> */
    public const EXTRA_PERMISSIONS = [
        self::MEDIA,
        self::MEDIA_TREE,
        self::MEDIA_UPLOAD,
        self::MEDIA_EDIT,
        self::MEDIA_FOLDERS,
    ];

    /** @var array<string, list<string>> */
    public const EXTRA_CRUD_ACTIONS = [
        'admin' => [
            self::ACTION_IMPERSONATE
        ],
        'user' => [
            self::ACTION_IMPERSONATE
        ],
    ];

    /** @var array<string, list<string>> */
    public const DISABLED_CRUD_ACTIONS = [
        'config' => [
            Action::NEW,
            Action::DETAIL,
            Action::EDIT,
            Action::DELETE,
        ],
        'settings' => [
            Action::NEW,
            Action::DETAIL,
            Action::EDIT,
            Action::DELETE,
        ],
    ];

    public static function normalize(string $permission): string
    {
        $parts = array_filter(explode('_', $permission));
        $parts = array_map(lcfirst(...), $parts);

        return implode('_', $parts);
    }

    public static function crud(string $crud): string
    {
        return self::normalize(self::CRUD.'_'.$crud);
    }

    public static function crudAction(string $crud, string $action): string
    {
        return self::normalize(self::CRUD.'_'.$crud.'_'.$action);
    }

    /**
     * @return list<string>
     */
    public static function disabledCrudKeys(): array
    {
        $keys = [];

        foreach (self::DISABLED_CRUD_ACTIONS as $crud => $actions) {
            foreach ($actions as $action) {
                $keys[] = self::normalize($crud.'_'.$action);
            }
        }

        return $keys;
    }
}
