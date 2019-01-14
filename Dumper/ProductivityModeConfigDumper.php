<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use sdShopEnvironment\Loader\ProductivityModeConfigLoader;
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
