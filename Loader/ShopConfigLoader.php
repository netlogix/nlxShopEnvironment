<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use sdShopEnvironment\Factory\ReflectionClassFactoryInterface;
use Shopware\Models\Category\Category;
use Shopware\Models\Customer\Group;
use Shopware\Models\Shop\Locale;
use Shopware\Models\Shop\Shop;

class ShopConfigLoader implements LoaderInterface
{
    const RELATION_LIST = [
        'CustomerGroup',
        'Category',
        'Locale',
        'Main',
    ];

    /** @var ObjectRepository */
    private $shopRepo;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ReflectionClassFactoryInterface */
    private $reflectionClassFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        ReflectionClassFactoryInterface $reflectionClassFactory
    ) {
        $this->entityManager = $entityManager;
        $this->reflectionClassFactory = $reflectionClassFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        $this->shopRepo = $this->entityManager->getRepository(Shop::class);
        $shopReflectionClass = $this->reflectionClassFactory->create(Shop::class);
        $shopConfigLoaderReflectionClass = $this->reflectionClassFactory->create(__CLASS__);

        foreach ($config as $id => $shopConfig) {
            $shop = $this->shopRepo->find($id);

            if (null === $shop) {
                $errorMessage = 'The loadable configuration contains a shop with an ID that is not yet created in database. ' .
                    PHP_EOL .
                    'We cannot create new shops with custom IDs at the moment, so this shop cannot be configured now.' .
                    PHP_EOL .
                    'Problematic shop: ' . $id . PHP_EOL . PHP_EOL;
                throw new \RuntimeException($errorMessage);
            }

            foreach ($shopConfig as $parameter => $value) {
                $setter = 'set' . $parameter;

                if (false === $shopReflectionClass->hasMethod($setter)) {
                    throw new \RuntimeException('Property could not be imported as it does not exist: ' .
                        $parameter . ' (shop: ' . $id . ')' . PHP_EOL);
                }

                if (\in_array($parameter, self::RELATION_LIST, true)) {
                    $getter = 'get' . $parameter;

                    if (false === $shopConfigLoaderReflectionClass->hasMethod($getter)) {
                        throw new \RuntimeException('Property could not be imported because the getter method not exist yet: ' .
                            $parameter . ' (shop: ' . $id . ')' . PHP_EOL);
                    }
                    $value = $this->$getter($value);
                }

                $shop->$setter($value);
            }

            $this->entityManager->persist($shop);
        }

        $this->entityManager->flush();
    }

    private function getCustomerGroup($customerGroupKey): Group
    {
        $customerGroupRepo = $this->entityManager->getRepository(Group::class);
        $group =  $customerGroupRepo->findOneBy(['key' => $customerGroupKey]);

        if (null === $group) {
            throw new \RuntimeException('The customer group key ' . $customerGroupKey . ' not exist');
        }

        return $group;
    }

    private function getCategory($categoryName): Category
    {
        $categoryRepo = $this->entityManager->getRepository(Category::class);
        $category = $categoryRepo->findOneBy(['name' => $categoryName]);

        if (null === $category) {
            throw new \RuntimeException('The customer group key ' . $categoryName . ' not exist');
        }

        return $category;
    }

    private function getLocale($localeKey): Locale
    {
        $LocaleRepo = $this->entityManager->getRepository(Locale::class);
        $locale = $LocaleRepo->findOneBy(['locale' => $localeKey]);

        if (null === $locale) {
            throw new \RuntimeException('The locale' . $localeKey . ' not exist');
        }

        return $locale;
    }

    private function getMain($shopId): Shop
    {
        $shop = $this->shopRepo->find($shopId);

        if (null === $shop) {
            throw new \RuntimeException('The shop' . $shopId . ' not exist');
        }

        return $shop;
    }
}
