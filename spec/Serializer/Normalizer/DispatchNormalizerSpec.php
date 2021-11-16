<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Serializer\Normalizer;

use nlxShopEnvironment\Serializer\Normalizer\DispatchNormalizer;
use PhpSpec\ObjectBehavior;
use Shopware\Models\Country\Country;
use Shopware\Models\Dispatch\Dispatch;
use Shopware\Models\Dispatch\ShippingCost;
use Shopware\Models\Payment\Payment;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class DispatchNormalizerSpec extends ObjectBehavior
{
    public function let(Serializer $serializer): void
    {
        $this->setSerializer($serializer);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DispatchNormalizer::class);
    }

    public function it_implements_correct_interfaces(): void
    {
        $this->shouldImplement(NormalizerInterface::class);
        $this->shouldImplement(DenormalizerInterface::class);
    }

    public function it_supports_dispatch_denormalization(): void
    {
        $this->supportsDenormalization([], Dispatch::class)->shouldBe(true);
    }

    public function it_does_not_support_other_denormalization(): void
    {
        $this->supportsDenormalization([], \stdClass::class)->shouldBe(false);
    }

    public function it_will_call_serializer_on_countries_attribute(
        \stdClass $object,
        Serializer $serializer
    ): void {
        $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = DispatchNormalizer::DISPATCH_PAYMENT_IGNORED_ATTRIBUTES;

        $serializer
            ->denormalize([1], Country::class . '[]', null, $context)
            ->shouldBeCalled()
            ->willReturn([]);

        $this->setAttributeValue($object, 'countries', [1], null, $context);
    }

    public function it_will_call_serializer_on_shops_attribute(
        \stdClass $object,
        Serializer $serializer
    ): void {
        $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = DispatchNormalizer::DISPATCH_PAYMENT_IGNORED_ATTRIBUTES;

        $serializer
            ->denormalize([1], ShippingCost::class . '[]', null, $context)
            ->shouldBeCalled()
            ->willReturn([]);

        $this->setAttributeValue($object, 'costsMatrix', [1], null, $context);
    }

    public function it_will_call_serializer_on_payments_attribute(
        \stdClass $object,
        Serializer $serializer
    ): void {
        $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = DispatchNormalizer::DISPATCH_PAYMENT_IGNORED_ATTRIBUTES;

        $serializer
            ->denormalize([1], Payment::class . '[]', null, $context)
            ->shouldBeCalled()
            ->willReturn([]);

        $this->setAttributeValue($object, 'payments', [1], null, $context);
    }
}
