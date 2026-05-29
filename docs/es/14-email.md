# Email

La base envía únicamente correo transaccional de serie (verificación y recuperación de contraseña) usando **Symfony Mailer**.

## Transporte

`config/packages/mailer.yaml`:

```yaml
framework:
    mailer:
        dsn: "%env(MAILER_DSN)%"
```

`MAILER_DSN` tiene por defecto `null://null`, por lo que no se envía ningún correo hasta que configures un transporte real en `.env.local` (SMTP, SendGrid, Mailgun, ...).

## `App\Service\MailService`

`src/Service/MailService.php`. Punto de entrada único para el correo saliente.

```php
public function send(
    string $subject,
    string $html,
    array $to = [],
    array $cc = [],
    array $bcc = [],
    array $attachments = [],
): bool;
```

Comportamiento:

- Construye la dirección `From` a partir de `AppConfig`: `new Address($config->senderEmail, $config->appName)`.
- En **entornos que no son producción**, todos los destinatarios se sustituyen por `appConfig.senderEmail` para que los correos de prueba nunca lleguen a usuarios reales.
- En producción, una lista `$to` vacía se trata como error (se registra y devuelve `false`).
- Los adjuntos se añaden con `attachFromPath()`.
- Los errores de transporte se registran mediante `LoggerInterface` y se suprimen; el método devuelve `true` solo cuando el transporte ha aceptado el mensaje.

## Plantillas

`templates/mails/`:

- `base.html.twig` — esqueleto HTML maestro para emails (layout reutilizable).
- `template.html.twig` — extiende la base y renderiza la estructura estándar de email transaccional: encabezado con el asunto, párrafos, botón(es) de llamada a la acción y párrafos posteriores al contenido.

`AuthController` renderiza `template.html.twig` con estas variables:

```php
$this->renderView('mails/template.html.twig', [
    'subject'     => ..., // string usado como H1 del email
    'content'     => [...], // array de párrafos encima del botón
    'buttons'     => [label => url, ...],
    'postContent' => [...], // array de párrafos debajo del botón
]);
```

## Dónde se utiliza

Actualmente solo en `App\Controller\AuthController`:

| Email                     | Asunto (clave i18n)                  | Se dispara cuando                                            |
| ------------------------- | ------------------------------------ | ------------------------------------------------------------ |
| Verificación de email     | `email.verify.subject`               | Tras un registro exitoso                                     |
| Solicitud de recuperación | `email.resetPasswordRequest.subject` | Se solicita recuperación para un usuario activo y verificado |

Las traducciones del asunto, párrafos del cuerpo, etiquetas de botones y contenido posterior residen bajo `email.*` en `translations/messages.es.yaml`.

## Añadir un nuevo email

1. Traduce el asunto y los fragmentos del cuerpo bajo `email.<nombre>.*` en `translations/messages.<locale>.yaml`.
2. Construye los arrays `subject`, `content`, `buttons` y `postContent` en el servicio o controlador que lo invoque.
3. Renderiza `mails/template.html.twig` (o `mails/base.html.twig` para un layout personalizado).
4. Envía mediante `MailService::send()`.
