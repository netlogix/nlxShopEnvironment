<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Loader\LoaderInterface;
use sdShopEnvironment\Loader\PaymentMethodsLoader;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Shop\Shop;

class PaymentMethodsLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ObjectRepository $paymentMethodsRepository,
        ObjectRepository $shopRepository
    ) {
        $entityManager
            ->getRepository(Payment::class)
            ->willReturn($paymentMethodsRepository);

        $shopRepository
            ->findBy(Argument::any())
            ->willReturn([]);

        $entityManager
            ->getRepository(Shop::class)
            ->willReturn($shopRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PaymentMethodsLoader::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_can_load_new_payment_methods(
        EntityManagerInterface $entityManager,
        ObjectRepository $paymentMethodsRepository
    ) {
        $paymentMethodsRepository
            ->findOneBy(['name' => 'new_payment_method'])
            ->willReturn(null);

        $data = ['new_payment_method' => ['description' => 'hello world', 'shops' => []]];

        $entityManager
            ->persist(Argument::type(Payment::class))
            ->shouldBeCalled();

        $entityManager
            ->flush()
            ->shouldBeCalled();

        $this->load($data);
    }

    public function it_can_load_existing_payment_methods(
        EntityManagerInterface $entityManager,
        ObjectRepository $paymentMethodsRepository,
        Payment $paymentMethod
    ) {
        $paymentMethodsRepository
            ->findOneBy(['name' => 'new_payment_method'])
            ->willReturn($paymentMethod);

        $data = ['new_payment_method' => ['description' => 'hello world', 'shops' => []]];

        $entityManager
            ->persist(Argument::any())
            ->shouldNotBeCalled();

        $paymentMethod
            ->fromArray($data['new_payment_method'])
            ->shouldBeCalled();

        $entityManager
            ->flush()
            ->shouldBeCalled();

        $this->load($data);
    }
}
