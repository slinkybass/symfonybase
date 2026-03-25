<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Handles outgoing email delivery for the application.
 *
 * Reads the sender address and application name from the session config object,
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
    ) {
    }

    /**
     * Sends an email.
     *
     * @param string   $subject     the email subject
     * @param string   $html        the HTML body
     * @param string[] $emails      primary recipients
     * @param string[] $cc          CC recipients
     * @param string[] $bcc         BCC recipients
     * @param string[] $attachments absolute file paths to attach
     *
     * @return bool true if the email was accepted by the transport, false otherwise
     */
    public function sendEmail(
        string $subject,
        string $html,
        array $emails = [],
        array $cc = [],
        array $bcc = [],
        array $attachments = [],
    ): bool {
        $config = $this->requestStack->getCurrentRequest()?->getSession()->get('config');

        $addressFrom = new Address($config->senderEmail, $config->appName);

        $email = (new Email())
            ->from($addressFrom)
            ->subject($subject)
            ->html($html);

        if ($this->params->get('kernel.environment') === 'prod') {
            foreach ($emails as $recipient) {
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
        } catch (TransportExceptionInterface) {
            return false;
        }
    }
}
