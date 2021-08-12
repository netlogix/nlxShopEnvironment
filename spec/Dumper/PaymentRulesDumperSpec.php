<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Dumper;

use nlxShopEnvironment\Dumper\DumperInterface;
use nlxShopEnvironment\Dumper\PaymentRulesDumper;
use PhpSpec\ObjectBehavior;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Payment\RuleSet;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaymentRulesDumperSpec extends ObjectBehavior
{
    public function let(
        ModelRepository $paymentRulesRepository,
        NormalizerInterface $normalizer
    ): void {
        $this->beConstructedWith($paymentRulesRepository, $normalizer);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PaymentRulesDumper::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_payment_rules(
        ModelRepository $paymentRulesRepository,
        NormalizerInterface $normalizer,
        RuleSet $ruleSet
    ): void {
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
