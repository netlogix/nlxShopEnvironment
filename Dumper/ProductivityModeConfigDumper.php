<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Loader\ProductivityModeConfigLoader;
use Shopware\Models\Plugin\Plugin;

class ProductivityModeConfigDumper implements DumperInterface
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
    public function dump()
    {
        /** @var Plugin $httpCache */
        $httpCache = $this->entityManager->getRepository(Plugin::class)->findOneBy(['name' => 'HttpCache']);

        return [
            ProductivityModeConfigLoader::SETTINGS_KEY => (bool) ($httpCache->getInstalled() && $httpCache->getActive()),
        ];
    }
}
