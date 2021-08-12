<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Serializer\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Serializer\Normalizer\DispatchPaymentNormalizer;
use PhpSpec\ObjectBehavior;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Payment\Payment;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DispatchPaymentNormalizerSpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $entityManager, ModelRepository $paymentRepository): void
    {
        $entityManager
            ->getRepository(Payment::class)
            ->willReturn($paymentRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DispatchPaymentNormalizer::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    public function it_supports_payment_normalization(Payment $payment): void
    {
        $this->supportsNormalization($payment)->shouldBe(true);
    }

    public function it_does_not_support_other_normalization(\stdClass $object): void
    {
        $this->supportsNormalization($object)->shouldBe(false);
    }

    public function it_supports_payment_denormalization(): void
    {
        $this->supportsDenormalization([], Payment::class)->shouldBe(true);
    }

    public function it_does_not_support_other_denormalization(): void
    {
        $this->supportsDenormalization([], \stdClass::class)->shouldBe(false);
    }

    public function it_returns_payment_id_on_normalization(Payment $payment): void
    {
        $payment
            ->getName()
            ->shouldBeCalled()
            ->willReturn('test');

        $this
            ->normalize($payment)
            ->shouldBe('test');
    }

    public function it_returns_payment_on_denormalization(
        Payment $payment,
        ModelRepository $paymentRepository
    ): void {
        $data = 'test';

        $paymentRepository
            ->findOneBy(['name' => 'test'])
            ->shouldBeCalled()
            ->willReturn($payment);

        $this
            ->denormalize($data, Payment::class)
            ->shouldBe($payment);
    }
}
