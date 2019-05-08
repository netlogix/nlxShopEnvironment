<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Media\Album;
use Shopware\Models\Media\Settings;

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
        $albumRepository = $this->entityManager->getRepository(Album::class);
        $settingsRepository = $this->entityManager->getRepository(Settings::class);

        foreach ($config as $key => $configElement) {
            $album = $albumRepository->find($key);
            if (null === $album) {
                $album = new Album();
                $this->entityManager->persist($album);
            }
            $album->setName($configElement['name']);
            $parentId = $configElement['parentID'];
            if (null === $parentId) {
                $album->setParent($parentId);
            } else {
                $album->setParent($albumRepository->find($parentId));
            }
            $album->setPosition($configElement['position']);
            $album->setGarbageCollectable($configElement['garbage_collectable']);

            $configSettingsElement = $configElement['settings'];
            $settings = $settingsRepository->findOneBy(['album' => $album]);
            if (null === $settings) {
                $settings = new Settings();
                $settings->setAlbum($album);
                $this->entityManager->persist($settings);
            }
            $settings->setCreateThumbnails($configSettingsElement['create_thumbnails']);
            $settings->setThumbnailSize($configSettingsElement['thumbnail_size']);
            $settings->setIcon($configSettingsElement['icon']);
            $settings->setThumbnailHighDpi($configSettingsElement['thumbnail_high_dpi']);
            $settings->setThumbnailQuality($configSettingsElement['thumbnail_quality']);
            $settings->setThumbnailHighDpiQuality($configSettingsElement['thumbnail_high_dpi_quality']);
        }

        $this->entityManager->flush();
    }
}
