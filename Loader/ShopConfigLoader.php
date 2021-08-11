<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Factory\ReflectionClassFactoryInterface;
use nlxShopEnvironment\Services\Shop\ShopEntityRelationHelperInterface;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Shop\Shop;

class ShopConfigLoader implements LoaderInterface
{
    /** @var ModelRepository */
    private $shopRepo;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var \ReflectionClass */
    private $shopReflectionClass;

    /** @var ShopEntityRelationHelperInterface */
    private $entityRelationHelper;

    public function __construct(
        EntityManagerInterface $entityManager,
        ReflectionClassFactoryInterface $reflectionClassFactory,
        ShopEntityRelationHelperInterface $entityRelationHelper
    ) {
        $this->entityManager = $entityManager;
        $this->entityRelationHelper = $entityRelationHelper;
        $this->shopReflectionClass = $reflectionClassFactory->create(Shop::class);
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config): void
    {
        $this->shopRepo = $this->entityManager->getRepository(Shop::class);

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

            $this->setConfig($shop, $shopConfig, $id);

            $this->entityManager->persist($shop);
        }

        $this->entityManager->flush();
    }

    /**
     * @param mixed[] $shopConfig
     */
    private function setConfig(
        Shop $shop,
        array $shopConfig,
        string $configId
    ): void {
        foreach ($shopConfig as $parameter => $value) {
            $setter = 'set' . $parameter;

            if (false === $this->shopReflectionClass->hasMethod($setter)) {
                throw new \RuntimeException('Property could not be imported as it does not exist: ' .
                    $parameter . ' (shop: ' . $configId . ')' . PHP_EOL);
            }

            if ($this->entityRelationHelper->isRelationField($parameter)) {
                $value = $this->entityRelationHelper->getEntity($parameter, $value);
            }

            $shop->$setter($value);
        }
    }
}
