<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Services\Shop;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Services\Shop\ShopEntityRelationHelper;
use nlxShopEnvironment\Services\Shop\ShopEntityRelationHelperInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Components\Model\ModelRepository;
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
    ): void {
        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ShopEntityRelationHelper::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(ShopEntityRelationHelperInterface::class);
    }

    public function it_returns_false_if_it_is_not_a_relation_field(): void
    {
        $entityName = 'CustomeWhat';

        $this->isRelationField($entityName)
            ->shouldReturn(false);
    }

    public function it_returns_true_if_it_is_a_relation_field(): void
    {
        $this->isRelationField(self::RELATION_ENTITY_LIST['CUSTOMER_GROUP'])
            ->shouldReturn(true);
    }

    public function it_returns_true_if_it_is_a_relation_field_with_capitalization(): void
    {
        $this->isRelationField(\strtoupper(self::RELATION_ENTITY_LIST['CUSTOMER_GROUP']))
            ->shouldReturn(true);
    }

    public function it_throws_an_exception_if_entity_not_registered(): void
    {
        $entityName = 'CustomeWhat';

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [$entityName, 'test']);
    }

    public function it_throws_an_exception_if_customer_group_not_found(
        EntityManagerInterface $entityManager,
        ModelRepository $customerGroup
    ): void {
        $entityManager->getRepository(Group::class)
            ->willReturn($customerGroup);

        $customerGroup->findOneBy(['key' => 'test'])
            ->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [self::RELATION_ENTITY_LIST['CUSTOMER_GROUP'], 'test']);
    }

    public function it_returns_a_customer_group_entity(
        EntityManagerInterface $entityManager,
        ModelRepository $customerGroupRepository,
        Customer $customerGroup
    ): void {
        $entityManager->getRepository(Group::class)
            ->willReturn($customerGroupRepository);

        $customerGroupRepository->findOneBy(['key' => 'test'])
            ->willReturn($customerGroup);

        $this->getEntity(self::RELATION_ENTITY_LIST['CUSTOMER_GROUP'], 'test')
            ->shouldReturn($customerGroup);
    }

    public function it_throws_an_exception_if_category_not_found(
        EntityManagerInterface $entityManager,
        ModelRepository $categoryRepository
    ): void {
        $entityManager->getRepository(Category::class)
            ->willReturn($categoryRepository);

        $categoryRepository->findOneBy(['name' => 'test'])
            ->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [self::RELATION_ENTITY_LIST['CATEGORY'], 'test']);
    }

    public function it_returns_a_category_entity(
        EntityManagerInterface $entityManager,
        ModelRepository $categoryRepository,
        Category $category
    ): void {
        $entityManager->getRepository(Category::class)
            ->willReturn($categoryRepository);

        $categoryRepository->findOneBy(['name' => 'test'])
            ->willReturn($category);

        $this->getEntity(self::RELATION_ENTITY_LIST['CATEGORY'], 'test')
            ->shouldReturn($category);
    }

    public function it_throws_an_exception_if_locale_not_found(
        EntityManagerInterface $entityManager,
        ModelRepository $localeRepository
    ): void {
        $entityManager->getRepository(Locale::class)
            ->willReturn($localeRepository);

        $localeRepository->findOneBy(['locale' => 'test'])
            ->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [self::RELATION_ENTITY_LIST['LOCALE'], 'test']);
    }

    public function it_returns_a_locale_entity(
        EntityManagerInterface $entityManager,
        ModelRepository $localeRepository,
        Locale $locale
    ): void {
        $entityManager->getRepository(Locale::class)
            ->willReturn($localeRepository);

        $localeRepository->findOneBy(['locale' => 'test'])
            ->willReturn($locale);

        $this->getEntity(self::RELATION_ENTITY_LIST['LOCALE'], 'test')
            ->shouldReturn($locale);
    }

    public function it_throws_an_exception_if_currency_not_found(
        EntityManagerInterface $entityManager,
        ModelRepository $currencyRepository
    ): void {
        $entityManager->getRepository(Currency::class)
            ->willReturn($currencyRepository);

        $currencyRepository->findOneBy(['currency' => 'test'])
            ->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [self::RELATION_ENTITY_LIST['CURRENCY'], 'test']);
    }

    public function it_returns_a_currency_entity(
        EntityManagerInterface $entityManager,
        ModelRepository $currencyRepository,
        Currency $currency
    ): void {
        $entityManager->getRepository(Currency::class)
            ->willReturn($currencyRepository);

        $currencyRepository->findOneBy(['currency' => 'test'])
            ->willReturn($currency);

        $this->getEntity(self::RELATION_ENTITY_LIST['CURRENCY'], 'test')
            ->shouldReturn($currency);
    }

    public function it_throws_an_exception_if_main_shop_not_found(
        EntityManagerInterface $entityManager,
        ModelRepository $mainShopRepository
    ): void {
        $entityManager->getRepository(Shop::class)
            ->willReturn($mainShopRepository);

        $mainShopRepository->findOneBy(['id' => 'test'])
            ->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [self::RELATION_ENTITY_LIST['MAIN'], 'test']);
    }

    public function it_returns_a_main_shop_entity(
        EntityManagerInterface $entityManager,
        ModelRepository $mainShopRepository,
        Shop $main
    ): void {
        $entityManager->getRepository(Shop::class)
            ->willReturn($mainShopRepository);

        $mainShopRepository->findOneBy(['id' => 'test'])
            ->willReturn($main);

        $this->getEntity(self::RELATION_ENTITY_LIST['MAIN'], 'test')
            ->shouldReturn($main);
    }

    public function it_throws_an_exception_if_fallback_shop_not_found(
        EntityManagerInterface $entityManager,
        ModelRepository $fallbackRepository
    ): void {
        $entityManager->getRepository(Shop::class)
            ->willReturn($fallbackRepository);

        $fallbackRepository->findOneBy(['id' => 'test'])
            ->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)
            ->during('getEntity', [self::RELATION_ENTITY_LIST['FALLBACK'], 'test']);
    }

    public function it_returns_a_fallback_shop_entity(
        EntityManagerInterface $entityManager,
        ModelRepository $fallbackRepository,
        Shop $fallback
    ): void {
        $entityManager->getRepository(Shop::class)
            ->willReturn($fallbackRepository);

        $fallbackRepository->findOneBy(['id' => 'test'])
            ->willReturn($fallback);

        $this->getEntity(self::RELATION_ENTITY_LIST['FALLBACK'], 'test')
            ->shouldReturn($fallback);
    }
}
