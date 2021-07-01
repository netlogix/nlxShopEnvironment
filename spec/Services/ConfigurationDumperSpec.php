<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Services;

use nlxShopEnvironment\DataTypes\DataTypeCollectorInterface;
use nlxShopEnvironment\Services\ConfigurationDumper;
use nlxShopEnvironment\Services\ConfigurationDumperInterface;
use PhpSpec\ObjectBehavior;

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
