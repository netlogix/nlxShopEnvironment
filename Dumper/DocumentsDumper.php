<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Document\Document;

class DocumentsDumper implements DumperInterface
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
        $config = [];

        $repository = $this->entityManager->getRepository(Document::class);
        $documents = $repository->findAll();

        /** @var Document $document */
        foreach ($documents as $document) {
            $config[$document->getKey()] = [
                'name'      => $document->getName(),
                'template'  => $document->getTemplate(),
                'numbers'   => $document->getNumbers(),
                'left'      => $document->getLeft(),
                'right'     => $document->getRight(),
                'top'       => $document->getTop(),
                'bottom'    => $document->getBottom(),
                'pagebreak' => $document->getPageBreak(),
            ];
        }

        return $config;
    }
}
