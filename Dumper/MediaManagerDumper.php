<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Media\Album;

class MediaManagerDumper implements DumperInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function dump()
    {
        $config = [];

        $repository = $this->entityManager->getRepository(Album::class);
        $albums = $repository->findAll();

        /** @var Album $album */
        foreach ($albums as $album) {
            $parent = $album->getParent();
            if (null !== $parent) {
                $parent = $parent->getId();
            }

            $settings = $album->getSettings();

            $config[$album->getId()] = [
                'name'  => $album->getName(),
                'parentID' => $parent,
                'position' => $album->getPosition(),
                'garbage_collectable' => $album->getGarbageCollectable(),
                'settings' => [
                    'create_thumbnails' => $settings->getCreateThumbnails(),
                    'thumbnail_size' => \implode(';', $settings->getThumbnailSize()),
                    'icon' => $settings->getIcon(),
                    'thumbnail_high_dpi' => $settings->isThumbnailHighDpi(),
                    'thumbnail_quality' => $settings->getThumbnailQuality(),
                    'thumbnail_high_dpi_quality' => $settings->getThumbnailHighDpiQuality(),
                ],
            ];
        }

        return $config;
    }
}
