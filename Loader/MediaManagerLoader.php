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
        if (false === \method_exists(Album::class, 'getGarbageCollectable')) {
            throw new \RuntimeException('The "MediaManagerLoader::load" method is not yet tested in this version of shopware. Only tested in shopware 5.4 and above!');
        }

        $albumRepository = $this->entityManager->getRepository(Album::class);
        $settingsRepository = $this->entityManager->getRepository(Settings::class);

        foreach ($config as $key => $configElement) {
            $album = $albumRepository->find($key);
            if (null === $album) {
                throw new \RuntimeException('The loading configuration contains a media manager album that is not yet created in the database. We cannot create such an album! AlbumId: ' . $key);
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
