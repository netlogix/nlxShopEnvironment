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
use sdShopEnvironment\Serializer\Normalizer\ShopNormalizer;
use Shopware\Models\Shop\Shop;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShopNormalizerSpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $entityManager, ObjectRepository $shopRepository)
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
        ObjectRepository $shopRepository
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
