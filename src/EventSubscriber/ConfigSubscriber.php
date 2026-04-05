<?php

namespace App\EventSubscriber;

use App\Model\AppConfig;
use App\Repository\ConfigRepository;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Loads application configuration on every main request and stores it in the session.
 *
 * Default values are defined in AppConfig and overridden by any Config entity found in
 * the database. The resulting config object is stored under the 'config' session key and
 * consumed by other services and templates throughout the request.
 *
 * Only the master request is processed; internal sub-requests are ignored to avoid
 * redundant database queries and unintended session writes.
 */
class ConfigSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ConfigRepository $configRepo,
        private readonly AssetMapperInterface $assetMapper,
    ) {
    }

    /**
     * Builds an AppConfig instance populated with application defaults.
     */
    private function buildDefaultConfig(): AppConfig
    {
        $config = new AppConfig();

        $config->appLogo = $this->assetMapper->getPublicPath('images/logo.png');
        $config->appFavicon = $this->assetMapper->getPublicPath('images/favicon.png');

        return $config;
    }

    /**
     * Builds the config object and stores it in the session.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $session = $event->getRequest()->getSession();

        if ($session->has('config')) {
            return;
        }

        $config = $this->buildDefaultConfig();
        $dbConfig = $this->configRepo->filterFirst();

        if ($dbConfig) {
            $config->appName = $dbConfig->getAppName() ?? $config->appName;
            $config->appColor = $dbConfig->getAppColor() ?? $config->appColor;
            $config->appLogo = $dbConfig->getAppLogo() ?? $config->appLogo;
            $config->appFavicon = $dbConfig->getAppFavicon() ?? $config->appFavicon;
            $config->appDescription = $dbConfig->getAppDescription() ?? $config->appDescription;
            $config->appKeywords = $dbConfig->getAppKeywords() ?? $config->appKeywords;
            $config->appTimezone = $dbConfig->getAppTimezone() ?? $config->appTimezone;
            $config->enablePublic = $dbConfig->isEnablePublic() ?? $config->enablePublic;
            $config->enableResetPassword = $dbConfig->isEnableResetPassword() ?? $config->enableResetPassword;
            $config->enableRegister = $dbConfig->isEnableRegister() ?? $config->enableRegister;
            $config->roleDefaultRegister = $dbConfig->getRoleDefaultRegister() ?? $config->roleDefaultRegister;
            $config->enableCookies = $dbConfig->isEnableCookies() ?? $config->enableCookies;
            $config->senderEmail = $dbConfig->getSenderEmail() ?? $config->senderEmail;
            $config->privacyText = $dbConfig->getPrivacyText() ?? $config->privacyText;
            $config->cookiesText = $dbConfig->getCookiesText() ?? $config->cookiesText;
        }

        $session->set('config', $config);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
