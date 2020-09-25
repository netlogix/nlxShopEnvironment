<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Dumper;

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use nlxShopEnvironment\Dumper\DumperInterface;
use nlxShopEnvironment\Dumper\PaymentRulesDumper;
use Shopware\Models\Payment\RuleSet;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaymentRulesDumperSpec extends ObjectBehavior
{
    public function let(
        ObjectRepository $paymentRulesRepository,
        NormalizerInterface $normalizer
    ) {
        $this->beConstructedWith($paymentRulesRepository, $normalizer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PaymentRulesDumper::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_payment_rules(
        ObjectRepository $paymentRulesRepository,
        NormalizerInterface $normalizer,
        RuleSet $ruleSet
    ) {
        $paymentRules = [$ruleSet];
        $paymentRulesRepository->findAll()
            ->willReturn($paymentRules);

        $normalizer->normalize($paymentRules)
            ->shouldBeCalled()
            ->willReturn(['id' => 123]);

        $this->dump()
            ->shouldBe(['id' => 123]);
    }
}
