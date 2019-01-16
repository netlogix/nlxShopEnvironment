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
use sdShopEnvironment\Dumper\DumperInterface;
use sdShopEnvironment\Dumper\PaymentMethodsDumper;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Shop\Shop;

class PaymentMethodsDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ObjectRepository $paymentMethodsRepository,
        ClassMetadata $classMetadata
    ) {
        $entityManager
            ->getRepository(Payment::class)
            ->willReturn($paymentMethodsRepository);

        $entityManager
            ->getClassMetadata(Payment::class)
            ->willReturn($classMetadata);

        $this->beConstructedWith($entityManager);
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
        ClassMetadata $classMetadata,
        Payment $paymentMethodOne,
        Payment $paymentMethodTwo,
        Shop $shopAssignedToPaymentMethodTwo
    ) {
        $shopAssignedToPaymentMethodTwo
            ->getId()
            ->willReturn(161);

        $paymentMethodOne
            ->getName()
            ->willReturn('ac');

        $paymentMethodOne
            ->getDescription()
            ->willReturn('Payment Method One');

        $paymentMethodOne
            ->getShops()
            ->willReturn(new ArrayCollection());

        $paymentMethodTwo
            ->getName()
            ->willReturn('ab');

        $paymentMethodTwo
            ->getDescription()
            ->willReturn('Payment Method Two');

        $paymentMethodTwo
            ->getShops()
            ->willReturn(new ArrayCollection([$shopAssignedToPaymentMethodTwo->getWrappedObject()]));

        $paymentMethodsRepository
            ->findAll()
            ->shouldBeCalled()
            ->willReturn([$paymentMethodOne, $paymentMethodTwo]);

        $classMetadata
            ->getFieldNames()
            ->willReturn(['id', 'description']);

        $this
            ->dump()
            ->shouldBeLike([
                'ac' => ['description' => 'Payment Method One', 'shops' => []],
                'ab' => ['description' => 'Payment Method Two', 'shops' => [161]],
            ]);
    }
}
