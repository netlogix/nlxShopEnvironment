<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Loader\LoaderInterface;
use nlxShopEnvironment\Loader\MediaManagerLoader;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Media\Album;
use Shopware\Models\Media\Settings;

class MediaManagerLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ModelRepository $albumRepository,
        ModelRepository $settingsRepository
    ) {
        $entityManager->getRepository(Album::class)
            ->willReturn($albumRepository);
        $entityManager->getRepository(Settings::class)
            ->willReturn($settingsRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MediaManagerLoader::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_can_handle_empty_config(
        EntityManagerInterface $entityManager,
        ModelRepository $albumRepository
    ) {
        if (false === \method_exists(Album::class, 'setGarbageCollectable')) {
            return;
        }

        $albumRepository->findOneBy(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load([]);
    }

    public function it_cannot_create_a_new_album(
        ModelRepository $albumRepository
    ) {
        if (false === \method_exists(Album::class, 'setGarbageCollectable')) {
            return;
        }

        $data = [
            -1 => [
                'name' => 'Album1',
                'parentID' => null,
                'position' => 13,
                'garbage_collectable' => true,
                'settings' => [
                    'create_thumbnails' => 0,
                    'thumbnail_size' => '8x8;1x1',
                    'icon' => 'sprite-target',
                    'thumbnail_high_dpi' => false,
                    'thumbnail_quality' => 80,
                    'thumbnail_high_dpi_quality' => 75,
                ],
            ],
        ];

        $albumRepository->find(-1)
            ->willReturn(null);

        $this->shouldThrow(\RuntimeException::class)
            ->during('load', [$data]);
    }

    public function it_can_update_existing_album_in_shopware_54_and_above(
        EntityManagerInterface $entityManager,
        ModelRepository $albumRepository,
        Album $album,
        ModelRepository $settingsRepository,
        Settings $settings
    ) {
        if (false === \method_exists(Album::class, 'setGarbageCollectable')) {
            return;
        }

        $data = [
            -1 => [
                'name' => 'Album1',
                'parentID' => null,
                'position' => 13,
                'garbage_collectable' => true,
                'settings' => [
                    'create_thumbnails' => 0,
                    'thumbnail_size' => '8x8;1x1',
                    'icon' => 'sprite-target',
                    'thumbnail_high_dpi' => false,
                    'thumbnail_quality' => 80,
                    'thumbnail_high_dpi_quality' => 75,
                ],
            ],
        ];

        $albumRepository->find(-1)
            ->willReturn($album);
        $entityManager->persist(Album::class)
            ->shouldNotBeCalled();

        $album->setName('Album1')
            ->shouldBeCalled();
        $album->setParent(null)
            ->shouldBeCalled();
        $album->setPosition(13)
            ->shouldBeCalled();
        $album->setGarbageCollectable(true)
            ->shouldBeCalled();

        $settingsRepository->findOneBy(['album' => $album])
            ->willReturn($settings);

        $settings->setCreateThumbnails(0)
            ->shouldBeCalled();
        $settings->setThumbnailSize('8x8;1x1')
            ->shouldBeCalled();
        $settings->setIcon('sprite-target')
            ->shouldBeCalled();
        $settings->setThumbnailHighDpi(false)
            ->shouldBeCalled();
        $settings->setThumbnailQuality(80)
            ->shouldBeCalled();
        $settings->setThumbnailHighDpiQuality(75)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($data);
    }

    // TODO: Adds specs for shopware prior 5.4
}
