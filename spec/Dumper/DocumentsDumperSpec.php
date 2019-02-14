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
use sdShopEnvironment\Dumper\DocumentsDumper;
use sdShopEnvironment\Dumper\DumperInterface;
use Shopware\Models\Document\Document;

class DocumentsDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ObjectRepository $documentsRepository
    ) {
        $entityManager
            ->getRepository(Document::class)
            ->willReturn($documentsRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(DocumentsDumper::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_empty_documents(
        ObjectRepository $documentsRepository
    ) {
        $documentsRepository->findAll()
            ->shouldBeCalled()
            ->willReturn([]);

        $this->dump()
            ->shouldBe([]);
    }

    public function it_can_dump_document(
        ObjectRepository $documentsRepository,
        Document $document1,
        Document $document2
    ) {
        $document1->getKey()
            ->willReturn('doc1');
        $document1->getName()
            ->willReturn('documentOne');
        $document1->getTemplate()
            ->willReturn('doc1.tpl');
        $document1->getNumbers()
            ->willReturn('doc01');
        $document1->getLeft()
            ->willReturn(99);
        $document1->getRight()
            ->willReturn(88);
        $document1->getTop()
            ->willReturn(77);
        $document1->getBottom()
            ->willReturn(66);
        $document1->getPageBreak()
            ->willReturn(55);

        $document2->getKey()
            ->willReturn('doc2');
        $document2->getName()
            ->willReturn('documentTwo');
        $document2->getTemplate()
            ->willReturn('doc2.tpl');
        $document2->getNumbers()
            ->willReturn('doc02');
        $document2->getLeft()
            ->willReturn(99);
        $document2->getRight()
            ->willReturn(88);
        $document2->getTop()
            ->willReturn(77);
        $document2->getBottom()
            ->willReturn(66);
        $document2->getPageBreak()
            ->willReturn(55);

        $documentsRepository->findAll()
            ->shouldBeCalled()
            ->willReturn([$document1, $document2]);

        $this->dump()
            ->shouldBeLike([
                'doc1' => [
                    'name'      => 'documentOne',
                    'template'  => 'doc1.tpl',
                    'numbers'   => 'doc01',
                    'left'      => 99,
                    'right'     => 88,
                    'top'       => 77,
                    'bottom'    => 66,
                    'pagebreak' => 55,
                ],
                'doc2' => [
                    'name'      => 'documentTwo',
                    'template'  => 'doc2.tpl',
                    'numbers'   => 'doc02',
                    'left'      => 99,
                    'right'     => 88,
                    'top'       => 77,
                    'bottom'    => 66,
                    'pagebreak' => 55,
                ],
            ]);
    }
}
