<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Serializer\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Serializer\Normalizer\ShopNormalizer;
use Shopware\Models\Shop\Shop;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShopNormalizerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(ShopNormalizer::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    public function it_support_shop_normalization(Shop $shop)
    {
        $this->supportsNormalization($shop)->shouldBe(true);
    }

    public function it_does_not_support_other_normalization(\stdClass $object)
    {
        $this->supportsNormalization($object)->shouldBe(false);
    }

    public function it_returns_shop_id_on_normalization(Shop $shop)
    {
        $shop
            ->getId()
            ->shouldBeCalled()
            ->willReturn(1);

        $this
            ->normalize($shop)
            ->shouldBe(1);
    }
}
