<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Bundle\PluginInstallerBundle\Service\InstallerService;
use Shopware\Models\Plugin\Plugin;

class ProductivityModeConfigLoader implements LoaderInterface
{
    const SETTINGS_KEY = 'productive_mode';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var InstallerService */
    private $pluginManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        InstallerService $pluginManager
    ) {
        $this->entityManager = $entityManager;
        $this->pluginManager = $pluginManager;
    }

    /**
     * {@inheritdoc}
     */
    public function load(?array $config): void
    {
        if (false === \array_key_exists(self::SETTINGS_KEY, $config)) {
            return;
        }

        /** @var Plugin $httpCache */
        $httpCache = $this->entityManager->getRepository(Plugin::class)->findOneBy(['name' => 'HttpCache']);

        if ((bool) $config[self::SETTINGS_KEY]) {
            $this->activeHttpCache($httpCache);
        } else {
            $this->deactivateHttpCache($httpCache);
        }
    }

    // The following two functions are heavily 'inspired' by \Shopware_Controllers_Backend_Performance .

    /**
     * Activate httpCache-Plugin
     */
    private function activeHttpCache(Plugin $httpCache): void
    {
        if (false === $httpCache->getInstalled()) {
            $this->pluginManager->installPlugin($httpCache);
        }

        if (false === $httpCache->getActive()) {
            $this->pluginManager->activatePlugin($httpCache);
        }
    }

    /**
     * Deactivate httpCache-Plugin
     */
    private function deactivateHttpCache(Plugin $httpCache): void
    {
        if (false === $httpCache->getActive()) {
            return;
        }

        $this->pluginManager->deactivatePlugin($httpCache);
    }
}
