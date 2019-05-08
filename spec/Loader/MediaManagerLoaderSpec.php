<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Loader\LoaderInterface;
use sdShopEnvironment\Loader\MediaManagerLoader;
use Shopware\Models\Media\Album;
use Shopware\Models\Media\Settings;

class MediaManagerLoaderSpec extends ObjectBehavior
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
        $this->shouldHaveType(MediaManagerLoader::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_can_handle_empty_config(
        EntityManagerInterface $entityManager,
        ObjectRepository $albumRepository
    ) {
        $albumRepository->findOneBy(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load([]);
    }

    public function it_can_create_new_album(
        EntityManagerInterface $entityManager,
        ObjectRepository $albumRepository
    ) {
        $data = [
            -1 => [
                'name' => 'Album1',
                'parentID' => null,
                'position' => 13,
                'garbage_collectable' => true,
            ],
        ];

        $albumRepository->find(-1)
            ->willReturn(null);
        $entityManager->persist(Argument::type(Album::class))
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($data);
    }

    public function it_can_update_existing_album(
        EntityManagerInterface $entityManager,
        ObjectRepository $albumRepository,
        Album $album
    ) {
        $data = [
            -1 => [
                'name' => 'Album1',
                'parentID' => null,
                'position' => 13,
                'garbage_collectable' => true,
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

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($data);
    }
}
