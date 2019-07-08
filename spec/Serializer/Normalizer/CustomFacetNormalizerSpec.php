<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Serializer\Normalizer;

use PhpSpec\ObjectBehavior;
use sdShopEnvironment\Serializer\Normalizer\CustomFacetNormalizer;
use sdShopEnvironment\Serializer\Normalizer\DispatchNormalizer;
use Shopware\Models\Country\Country;
use Shopware\Models\Dispatch\Dispatch;
use Shopware\Models\Dispatch\ShippingCost;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Search\CustomFacet;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class CustomFacetNormalizerSpec extends ObjectBehavior
{
    public function let(Serializer $serializer)
    {
        $this->setSerializer($serializer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CustomFacetNormalizer::class);
    }

    public function it_implements_correct_interfaces()
    {
        $this->shouldImplement(NormalizerInterface::class);
        $this->shouldImplement(DenormalizerInterface::class);
    }

    public function it_supports_dispatch_denormalization()
    {
        $this->supportsDenormalization([], CustomFacet::class)->shouldBe(true);
    }

    public function it_does_not_support_other_denormalization()
    {
        $this->supportsDenormalization([], \stdClass::class)->shouldBe(false);
    }
}
