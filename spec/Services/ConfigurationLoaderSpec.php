<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Services;

use PhpSpec\ObjectBehavior;
use sdShopEnvironment\DataTypes\DataTypeCollectorInterface;
use sdShopEnvironment\Services\ConfigurationLoader;
use sdShopEnvironment\Services\ConfigurationLoaderInterface;

class ConfigurationLoaderSpec extends ObjectBehavior
{
    public function let(
        DataTypeCollectorInterface $dataTypeCollector
    ) {
        $this->beConstructedWith(
            $dataTypeCollector
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
