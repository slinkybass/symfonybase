<?php

namespace App\Service;

use App\Model\AppConfig;
use App\Repository\ConfigRepository;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Resolves `AppConfig` from defaults, optional `Config` row overrides, and AssetMapper paths.
 *
 * The result is cached under a fixed key; `App\Entity\Config` updates clear that entry via
 * `App\EventListener\ConfigCacheListener`. Only scalar DTO fields are stored (e.g. default register role id),
 * never Doctrine entities.
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
     * Returns the cached DTO, building it on a miss. Drops the cache once if a legacy entry still
     * carried a removed `roleDefaultRegister` object property, then rebuilds.
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
