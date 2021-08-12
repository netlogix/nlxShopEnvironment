<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Factory\ReflectionClassFactoryInterface;
use nlxShopEnvironment\Loader\LoaderInterface;
use nlxShopEnvironment\Loader\ShopConfigLoader;
use nlxShopEnvironment\Services\Shop\ShopEntityRelationHelperInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Customer\Group;
use Shopware\Models\Shop\Shop;

class ShopConfigLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ReflectionClassFactoryInterface $reflectionClassFactory,
        \ReflectionClass $shopReflectionClass,
        ShopEntityRelationHelperInterface $entityRelationHelper,
        ModelRepository $shopRepository
    ): void {
        $entityManager
            ->getRepository(Shop::class)
            ->willReturn($shopRepository);

        $reflectionClassFactory->create(Shop::class)
            ->willReturn($shopReflectionClass);

        $this->beConstructedWith($entityManager, $reflectionClassFactory, $entityRelationHelper);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ShopConfigLoader::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_should_throw_an_exception_if_shop_not_exist(
        EntityManagerInterface $entityManager,
        ModelRepository $shopRepository,
        \ReflectionClass $shopReflectionClass
    ): void {
        $config = [1 => ['superVillain' => 'Joker']];

        $shopRepository->find(1)
            ->willReturn(null);

        $shopReflectionClass->hasMethod(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->flush()
            ->shouldNotBeCalled();

        $this->shouldThrow(\RuntimeException::class)
            ->during('load', [$config]);
    }

    public function it_should_throw_an_exception_if_setter_not_exist(
        EntityManagerInterface $entityManager,
        ModelRepository $shopRepository,
        \ReflectionClass $shopReflectionClass,
        Shop $shop
    ): void {
        $config = [1 => ['superVillain' => 'Joker']];

        $shopRepository->find(1)
            ->willReturn($shop);

        $shopReflectionClass->hasMethod('setsuperVillain')
            ->willReturn(false);

        $entityManager->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->flush()
            ->shouldNotBeCalled();

        $this->shouldThrow(\RuntimeException::class)
            ->during('load', [$config]);
    }

    public function it_can_load_empty(
        EntityManagerInterface $entityManager,
        ModelRepository $shopRepository,
        \ReflectionClass $shopReflectionClass
    ): void {
        $shopRepository->find(Argument::any())
            ->shouldNotBeCalled();

        $shopReflectionClass->hasMethod(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load([]);
    }

    public function it_can_load_if_config_is_emtpy(
        EntityManagerInterface $entityManager,
        ModelRepository $shopRepository,
        \ReflectionClass $shopReflectionClass,
        Shop $shop
    ): void {
        $config = [1 => []];

        $shopRepository->find(1)
            ->willReturn($shop);

        $shopReflectionClass->hasMethod(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->persist($shop)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($config);
    }

    public function it_can_update_existing_shop(
        EntityManagerInterface $entityManager,
        ModelRepository $shopRepository,
        \ReflectionClass $shopReflectionClass,
        ShopEntityRelationHelperInterface $entityRelationHelper,
        Shop $shop
    ): void {
        $config = [1 => ['Name' => 'Gotham']];

        $shopRepository->find(1)
            ->willReturn($shop);

        $shopReflectionClass->hasMethod('setName')
            ->willReturn(true);

        $shop->setName('Gotham')
            ->shouldBeCalled();

        $entityRelationHelper->isRelationField('Name')
            ->willReturn(false);

        $entityManager->persist($shop)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($config);
    }

    public function it_can_update_existing_shop_with_properties_with_a_relation(
        EntityManagerInterface $entityManager,
        ModelRepository $shopRepository,
        ShopEntityRelationHelperInterface $entityRelationHelper,
        Shop $shop,
        Group $group,
        \ReflectionClass $shopReflectionClass
    ): void {
        $config = [
            1 => [
                'CustomerGroup' => 'Villains',
            ],
        ];

        $shopRepository->find(1)
            ->willReturn($shop);

        $shopReflectionClass->hasMethod('setCustomerGroup')
            ->willReturn(true);

        $entityRelationHelper->isRelationField('CustomerGroup')
            ->willReturn(true);

        $entityRelationHelper->getEntity('CustomerGroup', 'Villains')
            ->willReturn($group);

        $shop->setCustomerGroup($group)
            ->shouldBeCalled();

        $entityManager->persist($shop)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($config);
    }
}
