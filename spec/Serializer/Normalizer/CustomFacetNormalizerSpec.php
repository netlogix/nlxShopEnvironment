<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Serializer\Normalizer;

use nlxShopEnvironment\Serializer\Normalizer\CustomFacetNormalizer;
use PhpSpec\ObjectBehavior;
use Shopware\Models\Search\CustomFacet;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class CustomFacetNormalizerSpec extends ObjectBehavior
{
    public function let(Serializer $serializer): void
    {
        $this->setSerializer($serializer);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CustomFacetNormalizer::class);
    }

    public function it_implements_correct_interfaces(): void
    {
        $this->shouldImplement(NormalizerInterface::class);
        $this->shouldImplement(DenormalizerInterface::class);
    }

    public function it_supports_dispatch_denormalization(): void
    {
        $this->supportsDenormalization([], CustomFacet::class)->shouldBe(true);
    }

    public function it_does_not_support_other_denormalization(): void
    {
        $this->supportsDenormalization([], \stdClass::class)->shouldBe(false);
    }
}
