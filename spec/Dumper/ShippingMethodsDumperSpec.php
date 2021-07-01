<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Dumper\DumperInterface;
use nlxShopEnvironment\Dumper\ShippingMethodsDumper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Dispatch\Dispatch;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShippingMethodsDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ModelRepository $shippingMethodsRepository,
        NormalizerInterface $normalizer
    ) {
        $entityManager
            ->getRepository(Dispatch::class)
            ->willReturn($shippingMethodsRepository);

        $this->beConstructedWith($entityManager, $normalizer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ShippingMethodsDumper::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_empty_shipping_methods(ModelRepository $shippingMethodsRepository)
    {
        $shippingMethodsRepository
            ->findAll()
            ->shouldBeCalled()
            ->willReturn([]);

        $this
            ->dump()
            ->shouldBe([]);
    }

    public function it_can_dump_shipping_methods(
        ModelRepository $shippingMethodsRepository,
        Dispatch $shippingMethodOne,
        Dispatch $shippingMethodTwo,
        NormalizerInterface $normalizer
    ) {
        $shippingMethodOne
            ->getId()
            ->willReturn(13);

        $shippingMethodTwo
            ->getId()
            ->willReturn(12);

        $shippingMethodsRepository
            ->findAll()
            ->shouldBeCalled()
            ->willReturn([$shippingMethodOne, $shippingMethodTwo]);

        $normalizer
            ->normalize(Argument::type(Dispatch::class))
            ->shouldBeCalledTimes(2)
            ->willReturn(['data' => 'data']);

        $this
            ->dump()
            ->shouldBeLike([
                13 => ['data' => 'data'],
                12 => ['data' => 'data'],
            ]);
    }
}
