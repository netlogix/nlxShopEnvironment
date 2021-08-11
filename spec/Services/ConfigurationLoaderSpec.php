<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Services;

use nlxShopEnvironment\DataTypes\DataTypeCollectorInterface;
use nlxShopEnvironment\Services\ConfigurationLoader;
use nlxShopEnvironment\Services\ConfigurationLoaderInterface;
use PhpSpec\ObjectBehavior;

class ConfigurationLoaderSpec extends ObjectBehavior
{
    public function let(
        DataTypeCollectorInterface $dataTypeCollector
    ): void {
        $this->beConstructedWith(
            $dataTypeCollector
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ConfigurationLoader::class);
    }

    public function it_implements_ConfigurationLoader_interface(): void
    {
        $this->shouldImplement(ConfigurationLoaderInterface::class);
    }
}
