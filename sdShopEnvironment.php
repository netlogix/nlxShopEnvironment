<?php

namespace sdShopEnvironment;

use Shopware\Components\Plugin;
use Shopware\Components\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
