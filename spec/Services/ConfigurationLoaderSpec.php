<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Services;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Services\ConfigurationLoader;
use sdShopEnvironment\Services\ConfigurationLoaderInterface;
use Shopware\Components\ConfigWriter;
use Shopware\Components\DependencyInjection\Container;

class ConfigurationLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ConfigWriter $configWriter,
        Connection $connection
    ) {
        $this->beConstructedWith(
            $entityManager,
            $configWriter,
            $connection
        );
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
