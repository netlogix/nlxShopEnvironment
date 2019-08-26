<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Loader;

use PhpSpec\ObjectBehavior;
use sdShopEnvironment\Loader\LoaderInterface;
use sdShopEnvironment\Loader\PaymentRulesLoader;

class PaymentRulesLoaderSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(PaymentRulesLoader::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }
}
