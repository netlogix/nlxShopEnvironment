<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Dumper\DumperInterface;
use nlxShopEnvironment\Dumper\PaymentMethodsDumper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Payment\Payment;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaymentMethodsDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ModelRepository $paymentMethodsRepository,
        NormalizerInterface $normalizer
    ): void {
        $entityManager
            ->getRepository(Payment::class)
            ->willReturn($paymentMethodsRepository);

        $this->beConstructedWith($entityManager, $normalizer);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PaymentMethodsDumper::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_empty_payment_methods(ModelRepository $paymentMethodsRepository): void
    {
        $paymentMethodsRepository
            ->findAll()
            ->shouldBeCalled()
            ->willReturn([]);

        $this
            ->dump()
            ->shouldBe([]);
    }

    public function it_can_dump_payment_methods(
        ModelRepository $paymentMethodsRepository,
        Payment $paymentMethodOne,
        Payment $paymentMethodTwo,
        NormalizerInterface $normalizer
    ): void {
        $paymentMethodOne
            ->getName()
            ->willReturn('ac');

        $paymentMethodTwo
            ->getName()
            ->willReturn('ab');

        $paymentMethodsRepository
            ->findAll()
            ->shouldBeCalled()
            ->willReturn([$paymentMethodOne, $paymentMethodTwo]);

        $normalizer
            ->normalize(Argument::type(Payment::class))
            ->shouldBeCalledTimes(2)
            ->willReturn(['data' => 'data']);

        $this
            ->dump()
            ->shouldBeLike([
                'ac' => ['data' => 'data'],
                'ab' => ['data' => 'data'],
            ]);
    }
}
