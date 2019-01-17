<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Serializer\Normalizer;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Serializer\Normalizer\PaymentNormalizer;
use Shopware\Models\Country\Country;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Shop\Shop;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PaymentNormalizerSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ObjectRepository $shopRepository,
        ObjectRepository $countryRepository
    ) {
        $entityManager
            ->getRepository(Shop::class)
            ->willReturn($shopRepository);

        $entityManager
            ->getRepository(Country::class)
            ->willReturn($countryRepository);

        $this->setEntityManager($entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PaymentNormalizer::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(DenormalizerInterface::class);
    }

    public function it_support_payment_denormalization(Payment $payment)
    {
        $this->supportsDenormalization($payment, Payment::class)->shouldBe(true);
    }

    public function it_does_not_support_other_normalization(\stdClass $object)
    {
        $this->supportsDenormalization($object, \stdClass::class)->shouldBe(false);
    }

    public function it_loads_shop_entites_on_denormalization(
        ObjectRepository $shopRepository
    ) {
        $shopRepository
            ->findBy(['id' => [1, 2]])
            ->shouldBeCalled();


        $this->denormalize(['shops' => [1, 2], 'countries' => []], Payment::class);
    }

    public function it_loads_country_entites_on_denormalization(
        ObjectRepository $countryRepository
    ) {
        $countryRepository
            ->findBy(['id' => [1, 2]])
            ->shouldBeCalled();


        $this->denormalize(['countries' => [1, 2], 'shops' => ''], Payment::class);
    }
}
