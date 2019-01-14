<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\ConfigWriter;

class ThemeConfigLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ConfigWriter */
    private $configWriter;

    /** @var Connection */
    private $connection;

    public function __construct(
        Connection $connection,
        EntityManagerInterface $entityManager,
        ConfigWriter $configWriter
    ) {
        $this->connection = $connection;
        $this->entityManager = $entityManager;
        $this->configWriter = $configWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        // TODO: Implement load() method.
    }
}
