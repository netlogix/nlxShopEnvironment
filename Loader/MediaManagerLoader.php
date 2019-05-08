<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Media\Album;

class MediaManagerLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function load($config)
    {
        $repository = $this->entityManager->getRepository(Album::class);

        foreach ($config as $key => $configElement) {
            $album = $repository->find($key);
            if (null === $album) {
                $album = new Album();
                $this->entityManager->persist($album);
            }
            $album->setName($configElement['name']);
            $parentId = $configElement['parentID'];
            if (null === $parentId) {
                $album->setParent($parentId);
            } else {
                $album->setParent($repository->find($parentId));
            }
            $album->setPosition($configElement['position']);
            $album->setGarbageCollectable($configElement['garbage_collectable']);
        }

        $this->entityManager->flush();
    }
}
