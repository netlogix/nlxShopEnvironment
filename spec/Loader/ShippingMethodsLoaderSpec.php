<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Loader\LoaderInterface;
use nlxShopEnvironment\Loader\ShippingMethodsLoader;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Dispatch\Dispatch;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ShippingMethodsLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ModelRepository $shippingMethodsRepository,
        DenormalizerInterface $denormalizer
    ) {
        $entityManager
            ->getRepository(Dispatch::class)
            ->willReturn($shippingMethodsRepository);

        $this->beConstructedWith($entityManager, $denormalizer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ShippingMethodsLoader::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_aborts_if_it_is_an_unknown_id(
        EntityManagerInterface $entityManager,
        ModelRepository $shippingMethodsRepository,
        DenormalizerInterface $denormalizer
    ) {
        $shippingMethodsRepository
            ->find(13)
            ->willReturn(null);

        $data = [13 => ['description' => 'hello world']];

        $denormalizer
            ->denormalize(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $entityManager
            ->flush()
            ->shouldBeCalled();

        $this->load($data);
    }

    public function it_can_load_existing_shipping_methods(
        EntityManagerInterface $entityManager,
        ModelRepository $shippingMethodsRepository,
        Dispatch $shippingMethod,
        DenormalizerInterface $denormalizer
    ) {
        $shippingMethodsRepository
            ->find(12)
            ->willReturn($shippingMethod);

        $data = [12 => ['description' => 'hello world']];

        $denormalizer
            ->denormalize(
                Argument::any(),
                Argument::any(),
                Argument::any(),
                Argument::withEntry('object_to_populate', $shippingMethod->getWrappedObject())
            )
            ->shouldBeCalled();

        $entityManager
            ->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager
            ->flush()
            ->shouldBeCalled();

        $this->load($data);
    }
}
