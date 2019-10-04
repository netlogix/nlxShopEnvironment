<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Services\Shop;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use sdShopEnvironment\Services\Shop\ShopEntityRelationHelper;
use sdShopEnvironment\Services\Shop\ShopEntityRelationHelperInterface;
use Shopware\Models\Category\Category;
use Shopware\Models\Customer\Customer;
use Shopware\Models\Customer\Group;
use Shopware\Models\Shop\Currency;
use Shopware\Models\Shop\Locale;
use Shopware\Models\Shop\Shop;

class ShopEntityRelationHelperSpec extends ObjectBehavior
{
    const RELATION_ENTITY_LIST = [
        'CUSTOMER_GROUP' => 'customergroup',
        'CATEGORY' => 'category',
        'LOCALE' => 'locale',
        'CURRENCY' => 'currency',
        'MAIN' => 'main',
        'FALLBACK' => 'fallback',
    ];

    public function let(
        EntityManagerInterface $entityManager
    ) {
        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ShopEntityRelationHelper::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(ShopEntityRelationHelperInterface::class);
    }

    public function it_returns_false_if_it_is_a_relation_field()
    {
        $entityName = 'CustomeWhat';

        $this->isRelationField($entityName)
            ->shouldReturn(false);
    }

    public function it_returns_true_if_it_is_a_relation_field()
    {
        $this->isRelationField(self::RELATION_ENTITY_LIST['CUSTOMER_GROUP'])
            ->shouldReturn(true);
    }

    public function it_throws_an_exception_if_entity_not_registered()
    {
        $entityName = 'CustomeWhat';

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [$entityName, 'test']);
    }

    public function it_throws_an_exception_if_customer_group_not_found(
        EntityManagerInterface $entityManager,
        ObjectRepository $customerGroup
    ) {
        $entityManager->getRepository(Group::class)
            ->willReturn($customerGroup);

        $customerGroup->findOneBy(['key' => 'test'])
            ->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [self::RELATION_ENTITY_LIST['CUSTOMER_GROUP'], 'test']);
    }

    public function it_returns_a_customer_group_entity(
        EntityManagerInterface $entityManager,
        ObjectRepository $customerGroupRepository,
        Customer $customerGroup
    ) {
        $entityManager->getRepository(Group::class)
            ->willReturn($customerGroupRepository);

        $customerGroupRepository->findOneBy(['key' => 'test'])
            ->willReturn($customerGroup);

        $this->getEntity(self::RELATION_ENTITY_LIST['CUSTOMER_GROUP'], 'test')
            ->shouldReturn($customerGroup);
    }

    public function it_throws_an_exception_if_category_not_found(
        EntityManagerInterface $entityManager,
        ObjectRepository $categoryRepository
    ) {
        $entityManager->getRepository(Category::class)
            ->willReturn($categoryRepository);

        $categoryRepository->findOneBy(['name' => 'test'])
            ->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [self::RELATION_ENTITY_LIST['CATEGORY'], 'test']);
    }

    public function it_returns_a_category_entity(
        EntityManagerInterface $entityManager,
        ObjectRepository $categoryRepository,
        Category $category
    ) {
        $entityManager->getRepository(Category::class)
            ->willReturn($categoryRepository);

        $categoryRepository->findOneBy(['name' => 'test'])
            ->willReturn($category);

        $this->getEntity(self::RELATION_ENTITY_LIST['CATEGORY'], 'test')
            ->shouldReturn($category);
    }

    public function it_throws_an_exception_if_locale_not_found(
        EntityManagerInterface $entityManager,
        ObjectRepository $localeRepository
    ) {
        $entityManager->getRepository(Locale::class)
            ->willReturn($localeRepository);

        $localeRepository->findOneBy(['name' => 'test'])
            ->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [self::RELATION_ENTITY_LIST['LOCALE'], 'test']);
    }

    public function it_returns_a_locale_entity(
        EntityManagerInterface $entityManager,
        ObjectRepository $localeRepository,
        Locale $locale
    ) {
        $entityManager->getRepository(Locale::class)
            ->willReturn($localeRepository);

        $localeRepository->findOneBy(['locale' => 'test'])
            ->willReturn($locale);

        $this->getEntity(self::RELATION_ENTITY_LIST['LOCALE'], 'test')
            ->shouldReturn($locale);
    }

    public function it_throws_an_exception_if_currency_not_found(
        EntityManagerInterface $entityManager,
        ObjectRepository $currencyRepository
    ) {
        $entityManager->getRepository(Currency::class)
            ->willReturn($currencyRepository);

        $currencyRepository->findOneBy(['currency' => 'test'])
            ->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [self::RELATION_ENTITY_LIST['CURRENCY'], 'test']);
    }

    public function it_returns_a_currency_entity(
        EntityManagerInterface $entityManager,
        ObjectRepository $currencyRepository,
        Currency $currency
    ) {
        $entityManager->getRepository(Currency::class)
            ->willReturn($currencyRepository);

        $currencyRepository->findOneBy(['currency' => 'test'])
            ->willReturn($currency);

        $this->getEntity(self::RELATION_ENTITY_LIST['CURRENCY'], 'test')
            ->shouldReturn($currency);
    }

    public function it_throws_an_exception_if_main_shop_not_found(
        EntityManagerInterface $entityManager,
        ObjectRepository $mainShopRepository
    ) {
        $entityManager->getRepository(Shop::class)
            ->willReturn($mainShopRepository);

        $mainShopRepository->findOneBy(['id' => 'test'])
            ->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [self::RELATION_ENTITY_LIST['MAIN'], 'test']);
    }

    public function it_returns_a_main_shop_entity(
        EntityManagerInterface $entityManager,
        ObjectRepository $mainShopRepository,
        Shop $main
    ) {
        $entityManager->getRepository(Shop::class)
            ->willReturn($mainShopRepository);

        $mainShopRepository->findOneBy(['id' => 'test'])
            ->willReturn($main);

        $this->getEntity(self::RELATION_ENTITY_LIST['MAIN'], 'test')
            ->shouldReturn($main);
    }

    public function it_throws_an_exception_if_fallback_shop_not_found(
        EntityManagerInterface $entityManager,
        ObjectRepository $fallbackRepository
    ) {
        $entityManager->getRepository(Shop::class)
            ->willReturn($fallbackRepository);

        $fallbackRepository->findOneBy(['id' => 'test'])
            ->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [self::RELATION_ENTITY_LIST['FALLBACK'], 'test']);
    }

    public function it_returns_a_fallback_shop_entity(
        EntityManagerInterface $entityManager,
        ObjectRepository $fallbackRepository,
        Shop $fallback
    ) {
        $entityManager->getRepository(Shop::class)
            ->willReturn($fallbackRepository);

        $fallbackRepository->findOneBy(['id' => 'test'])
            ->willReturn($fallback);

        $this->getEntity(self::RELATION_ENTITY_LIST['FALLBACK'], 'test')
            ->shouldReturn($fallback);
    }
}
