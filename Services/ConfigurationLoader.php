<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Services;

use Shopware\Components\DependencyInjection\Container;

class ConfigurationLoader implements ConfigurationLoaderInterface
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function loadConfiguration()
    {
        // TODO: Implement loadConfiguration() method.
    }
}
