<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Dumper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Dumper\DumperInterface;
use sdShopEnvironment\Dumper\PaymentMethodsDumper;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Shop\Shop;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaymentMethodsDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ObjectRepository $paymentMethodsRepository,
        NormalizerInterface $normalizer
    ) {
        $entityManager
            ->getRepository(Payment::class)
            ->willReturn($paymentMethodsRepository);

        $this->beConstructedWith($entityManager, $normalizer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PaymentMethodsDumper::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_empty_payment_methods(ObjectRepository $paymentMethodsRepository)
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
        ObjectRepository $paymentMethodsRepository,
        Payment $paymentMethodOne,
        Payment $paymentMethodTwo,
        NormalizerInterface $normalizer
    ) {
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