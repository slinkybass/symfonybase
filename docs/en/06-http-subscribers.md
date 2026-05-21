# HTTP event subscribers

Symfony kernel events drive cross-cutting behavior. All HTTP subscribers guard with `$event->isMainRequest()` and live under `src/EventSubscriber/`.

| Subscriber               | Event                        | Priority |
| ------------------------ | ---------------------------- | -------- |
| `LocaleSubscriber`       | `kernel.request`             | 40       |
| `MediaSubscriber`        | `kernel.request`             | 30       |
| `AccessSubscriber`       | `kernel.request`             | 7        |
| `InactiveUserSubscriber` | `kernel.request`             | 5        |
| `UserLoginSubscriber`    | `security.interactive_login` | 0        |

The Doctrine entity listener `App\EventListener\ConfigCacheListener` is covered in [configuration](03-configuration.md).

## LocaleSubscriber

`src/EventSubscriber/LocaleSubscriber.php`.

- Resolves the request locale from the session (`_locale` key), seeded by `_locale` request attribute or query string.
- The allow-list comes from the `LOCALES` env var (pipe-separated; injected through `services.yaml`).
- Falls back to `kernel.default_locale` (`es`) when the stored locale is invalid.
- Requires an active session (no-op otherwise).

See [i18n](15-i18n.md) for the full locale story.

## MediaSubscriber

`src/EventSubscriber/MediaSubscriber.php`.

- Only runs for the `file_manager` route with a `?conf=` query that maps to one of the `artgris_file_manager.conf` keys defined in `config/packages/artgris_file_manager.yaml`.
- Creates the configured upload directory on disk if missing (`%kernel.project_dir%/public/media/...`).

The Artgris config matrix:

| Conf key               | Directory             | Type  |
| ---------------------- | --------------------- | ----- |
| `public_all`           | `public/media`        | any   |
| `public_images`        | `public/media`        | image |
| `public_config_images` | `public/media/config` | image |
| `public_user_images`   | `public/media/user`   | image |

## AccessSubscriber

`src/EventSubscriber/AccessSubscriber.php`. Decides whether anonymous / authenticated users can hit certain public routes, based on [`AppConfig`](03-configuration.md) flags. Routes are grouped via private constants:

| Group constant    | Routes                                                                    | Behavior                                                                   |
| ----------------- | ------------------------------------------------------------------------- | -------------------------------------------------------------------------- |
| `PUBLIC_ROUTES`   | `home`                                                                    | Admins → `admin`; anonymous w/ `enablePublic = false` → `login`            |
| `LOGIN_ROUTES`    | `login`                                                                   | Logged-in users → `home`                                                   |
| `REGISTER_ROUTES` | `register`, `verify`                                                      | Logged-in → `home`; otherwise requires `enableRegister`                    |
| `RESET_ROUTES`    | `reset_password_request`, `reset_password_request_sent`, `reset_password` | Logged-in → `home`; otherwise requires `enableResetPassword`               |
| `PRIVACY_ROUTE`   | `privacy`                                                                 | Visible only when `appConfig.privacyText` is non-empty                     |
| `COOKIES_ROUTE`   | `cookies`                                                                 | Visible only when `appConfig.enableCookies` and `cookiesText` is non-empty |

When adding a route under any of these policies, register its name in the matching constant.

## InactiveUserSubscriber

`src/EventSubscriber/InactiveUserSubscriber.php`. After login, an account can still be deactivated mid-session. On every main request (except the `logout` route) this subscriber checks `User::isActive()` and triggers `Security::logout(false)` if false, replacing the response.

## UserLoginSubscriber

`src/EventSubscriber/UserLoginSubscriber.php`. Listens to `SecurityEvents::INTERACTIVE_LOGIN`. Throws `DisabledException` (and adds an error flash if a session exists) when the freshly authenticated user is:

- Not active → `app.messages.userDeactivated`.
- Not email-verified → `app.messages.userUnverified`.

Symfony then treats the login as failed.
