<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;

class SearchFieldsDumper implements DumperInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $searchFields = $this->entityManager->getConnection()->executeQuery(
            'SELECT * FROM `s_search_fields`'
        )->fetchAll();
        return $searchFields;
    }
}
