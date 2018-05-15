<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Services;

use PhpSpec\ObjectBehavior;
use sdShopEnvironment\Services\ConfigurationDumper;
use sdShopEnvironment\Services\ConfigurationDumperInterface;

class ConfigurationDumperSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ConfigurationDumper::class);
    }

    public function it_implements_ConfigurationDumper_interface()
    {
        $this->shouldImplement(ConfigurationDumperInterface::class);
    }
}
