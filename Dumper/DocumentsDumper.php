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
            $config[$this->getIdentitifer($document)] = [
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

    private function getIdentitifer(Document $document)
    {
        if (\method_exists($document, 'getKey')) {
            return $document->getKey();
        } else {
            return $document->getName();
        }
    }
}
