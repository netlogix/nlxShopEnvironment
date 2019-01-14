<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\ConfigWriter;

class ThemeConfigDumper implements DumperInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ConfigWriter */
    private $configWriter;

    public function __construct(
        EntityManagerInterface $entityManager,
        ConfigWriter $configWriter
    ) {
        $this->entityManager = $entityManager;
        $this->configWriter = $configWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        // TODO: Implement load() method.
        return [];
    }
}
