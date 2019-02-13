<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Services;

use PhpSpec\ObjectBehavior;
use sdShopEnvironment\DataTypes\DataTypeCollectorInterface;
use sdShopEnvironment\Services\ConfigurationDumper;
use sdShopEnvironment\Services\ConfigurationDumperInterface;

class ConfigurationDumperSpec extends ObjectBehavior
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
        $this->shouldHaveType(ConfigurationDumper::class);
    }

    public function it_implements_ConfigurationDumper_interface()
    {
        $this->shouldImplement(ConfigurationDumperInterface::class);
    }
}
