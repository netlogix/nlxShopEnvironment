<?php

namespace sdShopEnvironment;

use Shopware\Components\Plugin;
use Shopware\Components\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;

// We know, this makes problems in development environment (if Shopware is installed into vendor),
// but YAML component must be loaded here if this plugin is not installed via composer.
// To reduce errors in developement you can simply delete the vendor folder or comment these lines (but don't commit!)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Shopware-Plugin sdShopEnvironment.
 */
class sdShopEnvironment extends Plugin
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('sd_shop_environment.plugin_dir', $this->getPath());
        parent::build($container);
    }
}
