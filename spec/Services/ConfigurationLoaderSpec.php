<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Services;

use PhpSpec\ObjectBehavior;
use nlxShopEnvironment\DataTypes\DataTypeCollectorInterface;
use nlxShopEnvironment\Services\ConfigurationLoader;
use nlxShopEnvironment\Services\ConfigurationLoaderInterface;

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
