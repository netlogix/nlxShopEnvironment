<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
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

    public function it_can_dump_album_data(
        ObjectRepository $albumRepository,
        Album $album1,
        Album $album2
    ) {
        $this->prepareParametersForDump($albumRepository, $album1, $album2);

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
        ]);
        $dumpedAlbum2 = $dump->offsetGet(-2);
        $dumpedAlbum2->shouldBeLike([
            'name' => 'Album2',
            'parentID' => -1,
            'position' => 4,
            'garbage_collectable' => false,
        ]);
    }

    private function prepareParametersForDump(
        ObjectRepository $albumRepository,
        Album $album1,
        Album $album2
    ) {
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

        $albumRepository->findAll()
            ->shouldBeCalled()
            ->willReturn([$album1, $album2]);
    }
}
