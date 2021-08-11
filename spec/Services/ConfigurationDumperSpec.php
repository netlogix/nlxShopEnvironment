<?php declare(strict_types=1);

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
    ): void {
        $this->beConstructedWith(
            $dataTypeCollector
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ConfigurationDumper::class);
    }

    public function it_implements_ConfigurationDumper_interface(): void
    {
        $this->shouldImplement(ConfigurationDumperInterface::class);
    }
}
