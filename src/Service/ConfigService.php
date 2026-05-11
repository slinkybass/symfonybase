<?php

namespace App\Service;

use App\Model\AppConfig;
use App\Repository\ConfigRepository;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Provides the resolved application configuration, with database overrides and caching.
 */
final readonly class ConfigService
{
    private const string CACHE_KEY = 'app_config';

    public function __construct(
        private CacheInterface $cache,
        private ConfigRepository $configRepo,
        private AssetMapperInterface $assetMapper,
    ) {
    }

    /**
     * Returns the resolved application configuration, loading from cache or rebuilding from database.
     */
    public function get(): AppConfig
    {
        /** @var AppConfig $config */
        $config = $this->cache->get(self::CACHE_KEY, function () {
            $config = new AppConfig();

            $config->appLogo = $this->assetMapper->getPublicPath('images/logo.png');
            $config->appFavicon = $this->assetMapper->getPublicPath('images/favicon.png');

            $dbConfig = $this->configRepo->filterFirst();

            if (!$dbConfig) {
                return $config;
            }

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
            $config->roleDefaultRegisterId = $dbConfig->getRoleDefaultRegister()?->getId() ?? $config->roleDefaultRegisterId;
            $config->enableCookies = $dbConfig->isEnableCookies() ?? $config->enableCookies;
            $config->senderEmail = $dbConfig->getSenderEmail() ?? $config->senderEmail;
            $config->privacyText = $dbConfig->getPrivacyText() ?? $config->privacyText;
            $config->cookiesText = $dbConfig->getCookiesText() ?? $config->cookiesText;

            return $config;
        });

        // Heal legacy cached objects that still include a removed role entity property.
        if ($config->roleDefaultRegisterId === null && property_exists($config, 'roleDefaultRegister')) {
            $this->cache->delete(self::CACHE_KEY);

            return $this->get();
        }

        return $config;
    }
}
