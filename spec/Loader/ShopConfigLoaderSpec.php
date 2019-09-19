<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Factory\ReflectionClassFactoryInterface;
use sdShopEnvironment\Loader\LoaderInterface;
use sdShopEnvironment\Loader\ShopConfigLoader;
use Shopware\Models\Category\Category;
use Shopware\Models\Customer\Group;
use Shopware\Models\Shop\Locale;
use Shopware\Models\Shop\Shop;

class ShopConfigLoaderSpec extends ObjectBehavior
{
    const RELATION_LIST = [
        'CustomerGroup',
        'Category',
        'Locale',
        'Main',
    ];

    public function let(
        EntityManagerInterface $entityManager,
        ReflectionClassFactoryInterface $reflectionClassFactory,
        \ReflectionClass $shopReflectionClass,
        \ReflectionClass $shopConfigLoaderReflectionClass,
        ObjectRepository $shopRepository
    ) {
        $entityManager
            ->getRepository(Shop::class)
            ->willReturn($shopRepository);

        $reflectionClassFactory->create(Shop::class)
            ->willReturn($shopReflectionClass);

        $reflectionClassFactory->create(ShopConfigLoader::class)
            ->willReturn($shopConfigLoaderReflectionClass);

        $this->beConstructedWith($entityManager, $reflectionClassFactory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ShopConfigLoader::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_should_throw_an_exception_if_shop_not_exist(
        EntityManagerInterface $entityManager,
        ObjectRepository $shopRepository,
        \ReflectionClass $shopReflectionClass,
        \ReflectionClass $shopConfigLoaderReflectionClass
    ) {
        $config = [1 => ['superVillain' => 'Joker']];

        $shopRepository->find(1)
            ->willReturn(null);

        $shopReflectionClass->hasMethod(Argument::any())
            ->shouldNotBeCalled();

        $shopConfigLoaderReflectionClass->hasMethod(Argument::any())
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
        ObjectRepository $shopRepository,
        ReflectionClassFactoryInterface $reflectionClassFactory,
        \ReflectionClass $shopReflectionClass,
        \ReflectionClass $shopConfigLoaderReflectionClass,
        Shop $shop
    ) {
        $config = [1 => ['superVillain' => 'Joker']];

        $shopRepository->find(1)
            ->willReturn($shop);

        $shopReflectionClass->hasMethod('setSuperVillain')
            ->willReturn(false);

        $shopConfigLoaderReflectionClass->hasMethod(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->flush()
            ->shouldNotBeCalled();

        $this->shouldThrow(\RuntimeException::class)
            ->during('load', [$config]);
    }

    public function it_should_throw_an_exception_if_getter_not_exist(
        EntityManagerInterface $entityManager,
        ObjectRepository $shopRepository,
        ReflectionClassFactoryInterface $reflectionClassFactory,
        \ReflectionClass $shopReflectionClass,
        \ReflectionClass $shopConfigLoaderReflectionClass,
        Shop $shop
    ) {
        $config = [1 => ['CustomerGroup' => 'EK']];

        $shopRepository->find(1)
            ->willReturn($shop);

        $shopReflectionClass->hasMethod('setCustomerGroup')
            ->willReturn(true);

        $shopConfigLoaderReflectionClass->hasMethod('getCustomerGroup')
            ->willReturn(false);

        $entityManager->persist($shop)
            ->shouldNotBeCalled();

        $entityManager->flush()
            ->shouldNotBeCalled();

        $this->shouldThrow(\RuntimeException::class)
            ->during('load', [$config]);
    }

    public function it_can_load_empty(
        EntityManagerInterface $entityManager,
        ObjectRepository $shopRepository,
        \ReflectionClass $shopReflectionClass,
        \ReflectionClass $shopConfigLoaderReflectionClass
    ) {
        $shopRepository->find(Argument::any())
            ->shouldNotBeCalled();

        $shopReflectionClass->hasMethod(Argument::any())
            ->shouldNotBeCalled();

        $shopConfigLoaderReflectionClass->hasMethod(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load([]);
    }

    public function it_can_load_if_config_is_emtpy(
        EntityManagerInterface $entityManager,
        ObjectRepository $shopRepository,
        \ReflectionClass $shopReflectionClass,
        \ReflectionClass $shopConfigLoaderReflectionClass,
        Shop $shop
    ) {
        $config = [1 => []];

        $shopRepository->find(1)
            ->willReturn($shop);

        $shopReflectionClass->hasMethod(Argument::any())
            ->shouldNotBeCalled();

        $shopConfigLoaderReflectionClass->hasMethod(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->persist($shop)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($config);
    }

    public function it_can_update_existing_shop(
        EntityManagerInterface $entityManager,
        ObjectRepository $shopRepository,
        ReflectionClassFactoryInterface $reflectionClassFactory,
        \ReflectionClass $shopReflectionClass,
        \ReflectionClass $shopConfigLoaderReflectionClass,
        Shop $shop
    ) {
        $config = [1 => ['Name' => 'Gotham']];

        $shopRepository->find(1)
            ->willReturn($shop);

        $shopReflectionClass->hasMethod('setName')
            ->willReturn(true);

        $shop->setName('Gotham')
            ->shouldBeCalled();

        $shopConfigLoaderReflectionClass->hasMethod(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->persist($shop)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($config);
    }

    public function it_can_update_existing_shop_with_properties_from_the_relation_list(
        EntityManagerInterface $entityManager,
        ObjectRepository $shopRepository,
        ObjectRepository $groupRepository,
        ObjectRepository $categoryRepository,
        ObjectRepository $localeRepository,
        Shop $shop,
        Group $group,
        Category $category,
        Locale $locale,
        Shop $mainShop,
        \ReflectionClass $shopReflectionClass,
        \ReflectionClass $shopConfigLoaderReflectionClass
    ) {
        $config = [
            1 => [
                'CustomerGroup' => 'Villains',
                'Category' => 'Human',
                'Locale' => 'bd_Bad',
                'Main' => 2,
            ]
        ];

        $shopRepository->find(1)
            ->willReturn($shop);

        $this->reflectionClassHelper($shopReflectionClass, 'set', true);

        $this->reflectionClassHelper($shopConfigLoaderReflectionClass, 'get', true);

        $entityManager->getRepository(Group::class)
            ->willReturn($groupRepository);

        $groupRepository->findOneBy(['key' => 'Villains'])
            ->willReturn($group);

        $entityManager->getRepository(Category::class)
            ->willReturn($categoryRepository);

        $categoryRepository->findOneBy(['name' => 'Human'])
            ->willReturn($category);

        $entityManager->getRepository(Locale::class)
            ->willReturn($localeRepository);

        $localeRepository->findOneBy(['locale' => 'bd_Bad'])
            ->willReturn($locale);

        $shopRepository->find(2)
            ->willReturn($mainShop);

        $shop->setCustomerGroup($group)
            ->shouldBeCalled();

        $shop->setCategory($category)
            ->shouldBeCalled();

        $shop->setLocale($locale)
            ->shouldBeCalled();

        $shop->setMain($mainShop)
            ->shouldBeCalled();

        $entityManager->persist($shop)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($config);
    }

    private function reflectionClassHelper($reflectionClass, $method,  $returnValue) {

        foreach (self::RELATION_LIST as $relationMethod) {
            $reflectionClass->hasMethod($method . $relationMethod)
                ->willReturn($returnValue);
        }
    }
}
