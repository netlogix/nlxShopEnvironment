<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Dumper;

use PhpSpec\ObjectBehavior;
use sdShopEnvironment\Dumper\DumperInterface;
use sdShopEnvironment\Dumper\PaymentRulesDumper;

class PaymentRulesDumperSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(PaymentRulesDumper::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(DumperInterface::class);
    }
}
