<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Serializer\Normalizer;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Serializer\Normalizer\ShippingCostNormalizer;
use Shopware\Models\Dispatch\ShippingCost;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShippingCostNormalizerSpec extends ObjectBehavior
{
    public function let(
        AbstractNormalizer $normalizer,
        EntityManagerInterface $entityManager,
        ObjectRepository $shippingCostRepository
    ) {
        $entityManager
            ->getRepository(ShippingCost::class)
            ->willReturn($shippingCostRepository);

        $normalizer
            ->setIgnoredAttributes(Argument::any())
            ->shouldBeCalled();

        $this->beConstructedWith($normalizer, $entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ShippingCostNormalizer::class);
    }

    public function it_implements_correct_interfaces()
    {
        $this->shouldImplement(NormalizerInterface::class);
        $this->shouldImplement(DenormalizerInterface::class);
    }

    public function it_supports_shipping_cost_denormalization()
    {
        $this->supportsDenormalization([], ShippingCost::class)->shouldBe(true);
    }

    public function it_does_not_support_other_denormalization()
    {
        $this->supportsDenormalization([], \stdClass::class)->shouldBe(false);
    }

    public function it_supports_shipping_cost_normalization(ShippingCost $shippingCost)
    {
        $this->supportsNormalization($shippingCost)->shouldBe(true);
    }

    public function it_does_not_support_other_normalization(\stdClass $object)
    {
        $this->supportsNormalization($object)->shouldBe(false);
    }

    public function it_can_normalize(
        AbstractNormalizer $normalizer,
        ShippingCost $shippingCost
    ) {
        $normalizer
            ->normalize($shippingCost, Argument::any(), Argument::any())
            ->shouldBeCalled()
            ->willReturn(['test']);

        $this
            ->normalize($shippingCost)
            ->shouldBe(['test']);
    }

    public function it_can_denormalize_existing_shipping_cost(
        AbstractNormalizer $normalizer,
        ShippingCost $existingShippingCost,
        ObjectRepository $shippingCostRepository
    ) {
        $data = ['id' => 20];

        $shippingCostRepository
            ->find(20)
            ->shouldBeCalled()
            ->willReturn($existingShippingCost);

        $normalizer
            ->denormalize(
                $data,
                ShippingCost::class,
                null,
                Argument::withEntry('object_to_populate', $existingShippingCost->getWrappedObject())
            )
            ->shouldBeCalled()
            ->willReturn(['test']);

        $this
            ->denormalize($data, ShippingCost::class, null, [])
            ->shouldBe(['test']);
    }

    public function it_can_denormalize_new_shipping_cost(
        AbstractNormalizer $normalizer,
        ObjectRepository $shippingCostRepository
    ) {
        $data = ['id' => 20];

        $shippingCostRepository
            ->find(20)
            ->shouldBeCalled()
            ->willReturn(null);

        $normalizer
            ->denormalize(
                $data,
                ShippingCost::class,
                null,
                Argument::withKey('object_to_populate')
            )
            ->shouldBeCalled()
            ->willReturn(['test']);

        $this
            ->denormalize($data, ShippingCost::class, null, [])
            ->shouldBe(['test']);
    }
}
