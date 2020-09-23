<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Serializer\Normalizer;

use PhpSpec\ObjectBehavior;
use nlxShopEnvironment\Serializer\Normalizer\DispatchNormalizer;
use Shopware\Models\Country\Country;
use Shopware\Models\Dispatch\Dispatch;
use Shopware\Models\Dispatch\ShippingCost;
use Shopware\Models\Payment\Payment;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class DispatchNormalizerSpec extends ObjectBehavior
{
    public function let(Serializer $serializer)
    {
        $this->setSerializer($serializer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(DispatchNormalizer::class);
    }

    public function it_implements_correct_interfaces()
    {
        $this->shouldImplement(NormalizerInterface::class);
        $this->shouldImplement(DenormalizerInterface::class);
    }

    public function it_supports_dispatch_denormalization()
    {
        $this->supportsDenormalization([], Dispatch::class)->shouldBe(true);
    }

    public function it_does_not_support_other_denormalization()
    {
        $this->supportsDenormalization([], \stdClass::class)->shouldBe(false);
    }

    public function it_will_call_serializer_on_countries_attribute(
        \stdClass $object,
        Serializer $serializer
    ) {
        $serializer
            ->denormalize([1], Country::class . '[]', null)
            ->shouldBeCalled()
            ->willReturn([]);

        $this->setAttributeValue($object, 'countries', [1]);
    }

    public function it_will_call_serializer_on_shops_attribute(
        \stdClass $object,
        Serializer $serializer
    ) {
        $serializer
            ->denormalize([1], ShippingCost::class . '[]', null)
            ->shouldBeCalled()
            ->willReturn([]);

        $this->setAttributeValue($object, 'costsMatrix', [1]);
    }

    public function it_will_call_serializer_on_payments_attribute(
        \stdClass $object,
        Serializer $serializer
    ) {
        $serializer
            ->denormalize([1], Payment::class . '[]', null)
            ->shouldBeCalled()
            ->willReturn([]);

        $this->setAttributeValue($object, 'payments', [1]);
    }
}
