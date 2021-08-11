<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Services\Shop;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\Model\ModelEntity;
use Shopware\Models\Category\Category;
use Shopware\Models\Customer\Group;
use Shopware\Models\Shop\Currency;
use Shopware\Models\Shop\Locale;
use Shopware\Models\Shop\Shop;

class ShopEntityRelationHelper implements ShopEntityRelationHelperInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    const RELATION_ENTITY_LIST = [
        'CUSTOMER_GROUP' => 'customergroup',
        'CATEGORY' => 'category',
        'LOCALE' => 'locale',
        'CURRENCY' => 'currency',
        'MAIN' => 'main',
        'FALLBACK' => 'fallback',
    ];

    public function isRelationField(string $entityName): bool
    {
        return \in_array(\strtolower($entityName), self::RELATION_ENTITY_LIST, true);
    }

    public function getEntity(string $entityName, $value): ModelEntity
    {
        switch (\strtolower($entityName)) {
            case self::RELATION_ENTITY_LIST['CUSTOMER_GROUP']:
                $class = Group::class;
                $key = 'key';
                $errorMessage = 'The customer group key: ' . $entityName . ' not exist';
                return $this->find($class, $key, $value, $errorMessage);
            case self::RELATION_ENTITY_LIST['CATEGORY']:
                $class = Category::class;
                $key = 'name';
                $errorMessage = 'The category: ' . $entityName . ' not exist';
                return $this->find($class, $key, $value, $errorMessage);
            case self::RELATION_ENTITY_LIST['LOCALE']:
                $class = Locale::class;
                $key = 'locale';
                $errorMessage = 'The locale: ' . $entityName . ' not exist';
                return $this->find($class, $key, $value, $errorMessage);
            case self::RELATION_ENTITY_LIST['CURRENCY']:
                $class = Currency::class;
                $key = 'currency';
                $errorMessage = 'The currency: ' . $entityName . ' not exist';
                return $this->find($class, $key, $value, $errorMessage);
            case self::RELATION_ENTITY_LIST['MAIN']:
                $class = Shop::class;
                $key = 'id';
                $errorMessage = 'The main shop: ' . $entityName . ' not exist';
                return $this->find($class, $key, $value, $errorMessage);
            case self::RELATION_ENTITY_LIST['FALLBACK']:
                $class = Shop::class;
                $key = 'id';
                $errorMessage = 'The fallback shop: ' . $entityName . ' not exist';
                return $this->find($class, $key, $value, $errorMessage);
            default:
                throw new \RuntimeException('The entity: ' . $entityName . ' ist not registered yet');
        }
    }

    /**
     * @param mixed $value
     */
    private function find(
        string $class,
        string $key,
        $value,
        string $errorMessage
    ): ModelEntity {
        $repo = $this->entityManager->getRepository($class);
        $entity = $repo->findOneBy([$key => $value]);

        if (null === $entity) {
            throw new \RuntimeException($errorMessage);
        }

        return $entity;
    }
}
