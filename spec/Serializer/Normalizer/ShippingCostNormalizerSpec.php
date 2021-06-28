<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Serializer\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use nlxShopEnvironment\Serializer\Normalizer\ShippingCostNormalizer;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Dispatch\Dispatch;
use Shopware\Models\Dispatch\ShippingCost;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShippingCostNormalizerSpec extends ObjectBehavior
{
    public function let(
        AbstractNormalizer $normalizer,
        EntityManagerInterface $entityManager,
        ModelRepository $shippingCostRepository
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
        ShippingCost $shippingCost,
        Dispatch $dispatch
    ) {
        $shippingCost->getFrom()
            ->willReturn('0.100');
        $shippingCost->getValue()
            ->willReturn('0.20');
        $shippingCost->getFactor()
            ->willReturn('0.30');
        $shippingCost->getDispatch()
            ->willReturn($dispatch);

        $dispatch->getId()
            ->willReturn(42);

        $this
            ->normalize($shippingCost)
            ->shouldBe(
                [
                    'from'     => '0.100',
                    'value'    => '0.20',
                    'factor'   => '0.30',
                    'dispatch' => 42,
                ]
            );
    }

    public function it_can_denormalize_existing_shipping_cost(
        AbstractNormalizer $normalizer,
        ShippingCost $existingShippingCost,
        ModelRepository $shippingCostRepository
    ) {
        $data = [
            'from'     => '0.100',
            'dispatch' => 42,
        ];

        $shippingCostRepository
            ->findOneBy(
                [
                    'from'      => $data['from'],
                    'dispatch'  => $data['dispatch'],
                ]
            )
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
        ModelRepository $shippingCostRepository
    ) {
        $data = [
            'from'     => '0.100',
            'dispatch' => 42,
        ];

        $shippingCostRepository
            ->findOneBy(
                [
                    'from'      => $data['from'],
                    'dispatch'  => $data['dispatch'],
                ]
            )
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
