<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Serializer\PaymentMethodSerializer;
use sdShopEnvironment\Serializer\SerializerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Shop\Shop;


class PaymentMethodSerializerSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ObjectRepository $shopRepository,
        ClassMetadata $classMetadata
    ) {
        $entityManager
            ->getRepository(Shop::class)
            ->willReturn($shopRepository);

        $entityManager
            ->getClassMetadata(Payment::class)
            ->willReturn($classMetadata);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PaymentMethodSerializer::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(SerializerInterface::class);
    }

    public function it_can_serialize_payment_method(
        ClassMetadata $classMetadata,
        Payment $paymentMethod,
        Shop $shop
    ) {
        $shop
            ->getId()
            ->willReturn(161);

        $paymentMethod
            ->getName()
            ->willReturn('ac');

        $paymentMethod
            ->getDescription()
            ->willReturn('Payment Method One');

        $paymentMethod
            ->getShops()
            ->willReturn(new ArrayCollection([$shop->getWrappedObject()]));

        $classMetadata
            ->getFieldNames()
            ->willReturn(['id', 'description']);

        $this
            ->serialize($paymentMethod)
            ->shouldBeLike(['description' => 'Payment Method One', 'shops' => [161]]);
    }

    public function it_can_deserialize_payment_method(
        Payment $paymentMethod,
        ObjectRepository $shopRepository
    ) {
        $data = ['description' => 'hello world', 'shops' => [1]];

        $shopRepository
            ->findBy(['id' => [1]])
            ->shouldBeCalled()
            ->willReturn([]);

        $paymentMethod
            ->fromArray(['description' => 'hello world', 'shops' => []])
            ->shouldBeCalled();

        $this
            ->deserialize($paymentMethod, $data)
            ->shouldBe($paymentMethod);
    }
}
