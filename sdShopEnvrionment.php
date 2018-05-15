<?php

namespace sdShopEnvrionment;

use Shopware\Components\Plugin;
use Shopware\Components\Console\Application;
use sdShopEnvrionment\Commands\ShopEnvrionment;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Shopware-Plugin sdShopEnvrionment.
 */
class sdShopEnvrionment extends Plugin
{

    /**
    * @param ContainerBuilder $container
    */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('sd_shop_envrionment.plugin_dir', $this->getPath());
        parent::build($container);
    }

}
