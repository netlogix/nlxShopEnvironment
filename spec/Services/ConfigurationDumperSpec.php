<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PhpSpec\ObjectBehavior;
use sdShopEnvironment\Services\ConfigurationDumper;
use sdShopEnvironment\Services\ConfigurationDumperInterface;
use Shopware\Components\ConfigWriter;
use Shopware\Components\DependencyInjection\Container;

class ConfigurationDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ConfigWriter $configWriter
    ) {
        $this->beConstructedWith(
            $entityManager,
            $configWriter
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ConfigurationDumper::class);
    }

    public function it_implements_ConfigurationDumper_interface()
    {
        $this->shouldImplement(ConfigurationDumperInterface::class);
    }
}
