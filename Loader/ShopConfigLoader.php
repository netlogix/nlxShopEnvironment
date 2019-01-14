<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Shop\Shop;

class ShopConfigLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        $shopRepo = $this->entityManager->getRepository(Shop::class);

        foreach ($config as $id => $shopConfig) {
            $shop = $shopRepo->find($id);
            if (null === $shop) {
                echo
                    'The loadable configuration contains a shop with an ID that is not yet created in database. ' .
                    PHP_EOL .
                    'We cannot create new shops with custom IDs at the moment, so this shop cannot be configured now.' .
                    PHP_EOL .
                    'Problematic shop: ' . $id . PHP_EOL . PHP_EOL
                ;
                continue;
            }

            foreach ($shopConfig as $parameter => $value) {
                $reflectionClass = new \ReflectionClass(Shop::class);
                $setter = 'set' . $parameter;
                if (false === $reflectionClass->hasMethod($setter)) {
                    echo 'Property could not be imported as it does not exist: ' .
                        $parameter . ' (shop: ' . $id . ')' . PHP_EOL;
                }

                $shop->$setter($value);
            }

            $this->entityManager->persist($shop);
        }

        $this->entityManager->flush();
    }
}
