<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\sdShopEnvironment\Serializer\Normalizer;

use PhpSpec\ObjectBehavior;
use sdShopEnvironment\Serializer\Normalizer\PaymentNormalizer;
use Shopware\Models\Country\Country;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Shop\Shop;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class PaymentNormalizerSpec extends ObjectBehavior
{
    public function let(Serializer $serializer)
    {
        $this->setSerializer($serializer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PaymentNormalizer::class);
    }

    public function it_implements_correct_interfaces()
    {
        $this->shouldImplement(NormalizerInterface::class);
        $this->shouldImplement(DenormalizerInterface::class);
    }

    public function it_supports_payment_denormalization(Payment $payment)
    {
        $this->supportsDenormalization($payment, Payment::class)->shouldBe(true);
    }

    public function it_does_not_support_other_normalization(\stdClass $object)
    {
        $this->supportsDenormalization($object, \stdClass::class)->shouldBe(false);
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
            ->denormalize([1], Shop::class . '[]', null)
            ->shouldBeCalled()
            ->willReturn([]);

        $this->setAttributeValue($object, 'shops', [1]);
    }
}
