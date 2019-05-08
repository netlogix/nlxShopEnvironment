<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Dumper;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use sdShopEnvironment\Dumper\DumperInterface;
use sdShopEnvironment\Dumper\MediaManagerDumper;
use Shopware\Models\Media\Album;
use Shopware\Models\Media\Settings;

class MediaManagerDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ObjectRepository $albumRepository,
        ObjectRepository $settingsRepository
    ) {
        $entityManager->getRepository(Album::class)
            ->willReturn($albumRepository);
        $entityManager->getRepository(Settings::class)
            ->willReturn($settingsRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MediaManagerDumper::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_empty_albums(
        ObjectRepository $albumRepository
    ) {
        $albumRepository->findAll()
            ->shouldBeCalled()
            ->willReturn([]);

        $this->dump()
            ->shouldBe([]);
    }

    public function it_can_dump_album_data_in_shopware_54_and_above(
        ObjectRepository $albumRepository,
        Album $album1,
        Settings $settingsAlbum1,
        Album $album2,
        Settings $settingsAlbum2
    ) {
        if (false === \method_exists($album1->getWrappedObject(), 'getGarbageCollectable')) {
            return;
        }

        $this->prepareParametersForDump($albumRepository, $album1, $settingsAlbum1, $album2, $settingsAlbum2);

        $dump = $this->dump();
        $dump->shouldBeArray();
        $dump->shouldHaveCount(2);
        $dump->shouldHaveKey(-1);
        $dump->shouldHaveKey(-2);
        $dumpedAlbum1 = $dump->offsetGet(-1);
        $dumpedAlbum1->shouldBeLike([
            'name' => 'Album1',
            'parentID' => null,
            'position' => 3,
            'garbage_collectable' => true,
            'settings' => [
                'create_thumbnails' => 1,
                'thumbnail_size' => '2x2;5x5',
                'icon' => 'sprite-blue-folder',
                'thumbnail_high_dpi' => true,
                'thumbnail_quality' => 90,
                'thumbnail_high_dpi_quality' => 95,
            ],
        ]);
        $dumpedAlbum1->shouldHaveKey('settings');
        $dumpedSettigsAlbum1 = $dumpedAlbum1->offsetGet('settings');
        $dumpedSettigsAlbum1->shouldBeLike([
            'create_thumbnails' => 1,
            'thumbnail_size' => '2x2;5x5',
            'icon' => 'sprite-blue-folder',
            'thumbnail_high_dpi' => true,
            'thumbnail_quality' => 90,
            'thumbnail_high_dpi_quality' => 95,
        ]);
        $dumpedAlbum2 = $dump->offsetGet(-2);
        $dumpedAlbum2->shouldBeLike([
            'name' => 'Album2',
            'parentID' => -1,
            'position' => 4,
            'garbage_collectable' => false,
            'settings' => [
                'create_thumbnails' => 0,
                'thumbnail_size' => '8x8;1x1',
                'icon' => 'sprite-target',
                'thumbnail_high_dpi' => false,
                'thumbnail_quality' => 80,
                'thumbnail_high_dpi_quality' => 75,
            ],
        ]);
        $dumpedSettigsAlbum2 = $dumpedAlbum2->offsetGet('settings');
        $dumpedSettigsAlbum2->shouldBeLike([
            'create_thumbnails' => 0,
            'thumbnail_size' => '8x8;1x1',
            'icon' => 'sprite-target',
            'thumbnail_high_dpi' => false,
            'thumbnail_quality' => 80,
            'thumbnail_high_dpi_quality' => 75,
        ]);
    }

    // TODO: Adds specs for shopware prior 5.4

    private function prepareParametersForDump(
        ObjectRepository $albumRepository,
        Album $album1,
        Settings $settingsAlbum1,
        Album $album2,
        Settings $settingsAlbum2
    ) {
        $settingsAlbum1->getCreateThumbnails()
            ->willReturn(1);
        $settingsAlbum1->getThumbnailSize()
            ->willReturn([
                '2x2',
                '5x5',
            ]);
        $settingsAlbum1->getIcon()
            ->willReturn('sprite-blue-folder');
        $settingsAlbum1->isThumbnailHighDpi()
            ->willReturn(true);
        $settingsAlbum1->getThumbnailQuality()
            ->willReturn(90);
        $settingsAlbum1->getThumbnailHighDpiQuality()
            ->willReturn(95);

        $album1->getId()
            ->willReturn(-1);
        $album1->getName()
            ->willReturn('Album1');
        $album1->getParent()
            ->willReturn(null);
        $album1->getPosition()
            ->willReturn(3);
        $album1->getGarbageCollectable()
            ->willReturn(true);
        $album1->getSettings()
            ->willReturn($settingsAlbum1);

        $settingsAlbum2->getCreateThumbnails()
            ->willReturn(0);
        $settingsAlbum2->getThumbnailSize()
            ->willReturn([
                '8x8',
                '1x1',
            ]);
        $settingsAlbum2->getIcon()
            ->willReturn('sprite-target');
        $settingsAlbum2->isThumbnailHighDpi()
            ->willReturn(false);
        $settingsAlbum2->getThumbnailQuality()
            ->willReturn(80);
        $settingsAlbum2->getThumbnailHighDpiQuality()
            ->willReturn(75);

        $album2->getId()
            ->willReturn(-2);
        $album2->getName()
            ->willReturn('Album2');
        $album2->getParent()
            ->willReturn($album1);
        $album2->getPosition()
            ->willReturn(4);
        $album2->getGarbageCollectable()
            ->willReturn(false);
        $album2->getSettings()
            ->willReturn($settingsAlbum2);

        $albumRepository->findAll()
            ->shouldBeCalled()
            ->willReturn([$album1, $album2]);
    }
}
