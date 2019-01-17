<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Loader;

use PhpSpec\ObjectBehavior;
use sdShopEnvironment\Loader\LoaderInterface;
use sdShopEnvironment\Loader\ShippingMethodsLoader;

class ShippingMethodsLoaderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ShippingMethodsLoader::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }
}
