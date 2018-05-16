<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Services;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Services\ConfigurationLoader;
use sdShopEnvironment\Services\ConfigurationLoaderInterface;
use Shopware\Components\DependencyInjection\Container;

class ConfigurationLoaderSpec extends ObjectBehavior
{
    public function let(Container $container)
    {
        $this->beConstructedWith($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ConfigurationLoader::class);
    }

    public function it_implements_ConfigurationLoader_interface()
    {
        $this->shouldImplement(ConfigurationLoaderInterface::class);
    }
}
