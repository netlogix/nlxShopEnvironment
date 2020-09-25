<?php

namespace nlxShopEnvironment;

use nlxShopEnvironment\Components\CompilerPass\DataTypeCompilerPass;
use Shopware\Components\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class nlxShopEnvironment extends Plugin
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DataTypeCompilerPass());
    }
}
