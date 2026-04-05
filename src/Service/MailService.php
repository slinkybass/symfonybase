<?php

namespace App\Service;

use App\Model\AppConfig;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

/**
 * Handles outgoing email delivery for the application.
 *
 * Reads the sender address and application name from the session AppConfig object,
 * and delegates transport to Symfony Mailer.
 *
 * In non-production environments all recipients are replaced by the configured
 * sender address, so test emails never reach real users.
 */
class MailService
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ParameterBagInterface $params,
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Sends an email.
     *
     * @param string[] $to          primary recipients
     * @param string[] $cc          CC recipients
     * @param string[] $bcc         BCC recipients
     * @param string[] $attachments absolute file paths to attach
     *
     * @return bool true if the email was accepted by the transport, false otherwise
     */
    public function send(
        string $subject,
        string $html,
        array $to = [],
        array $cc = [],
        array $bcc = [],
        array $attachments = [],
    ): bool {
        /** @var AppConfig|null $config */
        $config = $this->requestStack->getSession()->get('config');

        if (!$config instanceof AppConfig) {
            $this->logger->error('MailService: config not found in session.');
            return false;
        }

        $isProd = $this->params->get('kernel.environment') === 'prod';

        if ($isProd && empty($to)) {
            $this->logger->error('MailService: no recipients provided.');
            return false;
        }

        $email = (new Email())
            ->from(new Address($config->senderEmail, $config->appName))
            ->subject($subject)
            ->html($html);

        if ($isProd) {
            foreach ($to as $recipient) {
                $email->addTo($recipient);
            }
            foreach ($cc as $recipient) {
                $email->addCc($recipient);
            }
            foreach ($bcc as $recipient) {
                $email->addBcc($recipient);
            }
        } else {
            $email->to($config->senderEmail);
        }

        foreach ($attachments as $path) {
            $email->attachFromPath($path);
        }

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('MailService: failed to send email.', [
                'subject' => $subject,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
    }
}