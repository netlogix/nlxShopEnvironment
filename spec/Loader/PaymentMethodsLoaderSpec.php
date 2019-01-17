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
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PaymentMethodsLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ObjectRepository $paymentMethodsRepository,
        DenormalizerInterface $denormalizer
    ) {
        $entityManager
            ->getRepository(Payment::class)
            ->willReturn($paymentMethodsRepository);

        $this->beConstructedWith($entityManager, $denormalizer);
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
        ObjectRepository $paymentMethodsRepository,
        DenormalizerInterface $denormalizer
    ) {
        $paymentMethodsRepository
            ->findOneBy(['name' => 'new_payment_method'])
            ->willReturn(null);

        $data = ['new_payment_method' => ['description' => 'hello world']];

        $denormalizer
            ->denormalize(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->shouldBeCalled();

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
        Payment $paymentMethod,
        DenormalizerInterface $denormalizer
    ) {
        $paymentMethodsRepository
            ->findOneBy(['name' => 'new_payment_method'])
            ->willReturn($paymentMethod);

        $data = ['new_payment_method' => ['description' => 'hello world', 'shops' => []]];

        $denormalizer
            ->denormalize(Argument::any(), Argument::any(), Argument::any(), Argument::withEntry('object_to_populate', $paymentMethod->getWrappedObject()))
            ->shouldBeCalled();

        $entityManager
            ->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager
            ->flush()
            ->shouldBeCalled();

        $this->load($data);
    }
}
