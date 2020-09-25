<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Serializer\Normalizer;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use nlxShopEnvironment\Serializer\Normalizer\DispatchPaymentNormalizer;
use Shopware\Models\Payment\Payment;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DispatchPaymentNormalizerSpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $entityManager, ObjectRepository $paymentRepository)
    {
        $entityManager
            ->getRepository(Payment::class)
            ->willReturn($paymentRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(DispatchPaymentNormalizer::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    public function it_supports_payment_normalization(Payment $payment)
    {
        $this->supportsNormalization($payment)->shouldBe(true);
    }

    public function it_does_not_support_other_normalization(\stdClass $object)
    {
        $this->supportsNormalization($object)->shouldBe(false);
    }

    public function it_supports_payment_denormalization()
    {
        $this->supportsDenormalization([], Payment::class)->shouldBe(true);
    }

    public function it_does_not_support_other_denormalization()
    {
        $this->supportsDenormalization([], \stdClass::class)->shouldBe(false);
    }

    public function it_returns_payment_id_on_normalization(Payment $payment)
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
        ObjectRepository $paymentRepository
    ) {
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
