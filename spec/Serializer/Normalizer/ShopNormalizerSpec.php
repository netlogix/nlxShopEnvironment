<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Serializer\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Serializer\Normalizer\ShopNormalizer;
use PhpSpec\ObjectBehavior;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Shop\Shop;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShopNormalizerSpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $entityManager, ModelRepository $shopRepository)
    {
        $entityManager
            ->getRepository(Shop::class)
            ->willReturn($shopRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ShopNormalizer::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    public function it_supports_shop_normalization(Shop $shop)
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

    public function it_supports_shop_denormalization()
    {
        $this->supportsDenormalization([], Shop::class)->shouldBe(true);
    }

    public function it_does_not_support_other_denormalization()
    {
        $this->supportsDenormalization([], \stdClass::class)->shouldBe(false);
    }

    public function it_returns_shop_on_denormalization(
        Shop $shop,
        ModelRepository $shopRepository
    ) {
        $data = 12;

        $shopRepository
            ->find(12)
            ->shouldBeCalled()
            ->willReturn($shop);

        $this
            ->denormalize($data, Shop::class)
            ->shouldBe($shop);
    }
}
