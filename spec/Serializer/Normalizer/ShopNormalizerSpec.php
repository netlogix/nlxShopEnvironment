<?php declare(strict_types=1);

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
    public function let(EntityManagerInterface $entityManager, ModelRepository $shopRepository): void
    {
        $entityManager
            ->getRepository(Shop::class)
            ->willReturn($shopRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ShopNormalizer::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    public function it_supports_shop_normalization(Shop $shop): void
    {
        $this->supportsNormalization($shop)->shouldBe(true);
    }

    public function it_does_not_support_other_normalization(\stdClass $object): void
    {
        $this->supportsNormalization($object)->shouldBe(false);
    }

    public function it_returns_shop_id_on_normalization(Shop $shop): void
    {
        $shop
            ->getId()
            ->shouldBeCalled()
            ->willReturn(1);

        $this
            ->normalize($shop)
            ->shouldBe(1);
    }

    public function it_supports_shop_denormalization(): void
    {
        $this->supportsDenormalization([], Shop::class)->shouldBe(true);
    }

    public function it_does_not_support_other_denormalization(): void
    {
        $this->supportsDenormalization([], \stdClass::class)->shouldBe(false);
    }

    public function it_returns_shop_on_denormalization(
        Shop $shop,
        ModelRepository $shopRepository
    ): void {
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
