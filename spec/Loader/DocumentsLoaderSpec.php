<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Loader\DocumentsLoader;
use sdShopEnvironment\Loader\LoaderInterface;
use Shopware\Models\Document\Document;
use Webmozart\Assert\Assert;

class DocumentsLoaderSpec extends ObjectBehavior
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
        $this->shouldHaveType(DocumentsLoader::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_can_load_empty(
        EntityManagerInterface $entityManager,
        ObjectRepository $documentsRepository
    ) {
        $documentsRepository->findOneBy(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load([]);
    }

    public function it_can_update_existing_document_for_shopware_since_5_5(
        EntityManagerInterface $entityManager,
        ObjectRepository $documentsRepository,
        Document $document1
    ) {
        if (false === \method_exists($document1->getWrappedObject(), 'getKey')) {
            return;
        }

        $data = [
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
        ];

        $documentsRepository->findOneBy(['key' => 'doc1'])
            ->willReturn($document1);

        $document1->setName('documentOne')
            ->shouldBeCalled();
        $document1->setTemplate('doc1.tpl')
            ->shouldBeCalled();
        $document1->setNumbers('doc01')
            ->shouldBeCalled();
        $document1->setLeft(99)
            ->shouldBeCalled();
        $document1->setRight(88)
            ->shouldBeCalled();
        $document1->setTop(77)
            ->shouldBeCalled();
        $document1->setBottom(66)
            ->shouldBeCalled();
        $document1->setPageBreak(55)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($data);
    }

    public function it_can_create_new_document_for_shopware_since_5_5(
        EntityManagerInterface $entityManager,
        ObjectRepository $documentsRepository,
        Document $document1
    ) {
        if (false === \method_exists($document1->getWrappedObject(), 'getKey')) {
            return;
        }

        $data = [
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
        ];

        $documentsRepository->findOneBy(['key' => 'doc1'])
            ->willReturn(null);

        $persistedDocument = null;
        $entityManager->persist(Argument::that(function ($document) use (&$persistedDocument) {
            if (false === \is_a($document, Document::class)) {
                return false;
            }

            $persistedDocument = $document;
            return true;
        }))
            ->shouldBeCalled();
        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($data);

        Assert::eq($persistedDocument->getKey(), 'doc1');
        Assert::eq($persistedDocument->getName(), 'documentOne');
        Assert::eq($persistedDocument->getTemplate(), 'doc1.tpl');
        Assert::eq($persistedDocument->getNumbers(), 'doc01');
        Assert::eq($persistedDocument->getLeft(), 99);
        Assert::eq($persistedDocument->getRight(), 88);
        Assert::eq($persistedDocument->getTop(), 77);
        Assert::eq($persistedDocument->getBottom(), 66);
        Assert::eq($persistedDocument->getPageBreak(), 55);
    }

    public function it_can_update_existing_document_for_older_shopware(
        EntityManagerInterface $entityManager,
        ObjectRepository $documentsRepository,
        Document $document1
    ) {
        if (true === \method_exists($document1->getWrappedObject(), 'getKey')) {
            return;
        }

        $data = [
            'documentOne' => [
                'name'      => 'documentOne',
                'template'  => 'doc1.tpl',
                'numbers'   => 'doc01',
                'left'      => 99,
                'right'     => 88,
                'top'       => 77,
                'bottom'    => 66,
                'pagebreak' => 55,
            ],
        ];

        $documentsRepository->findOneBy(['name' => 'documentOne'])
            ->willReturn($document1);

        $document1->setName('documentOne')
            ->shouldBeCalled();
        $document1->setTemplate('doc1.tpl')
            ->shouldBeCalled();
        $document1->setNumbers('doc01')
            ->shouldBeCalled();
        $document1->setLeft(99)
            ->shouldBeCalled();
        $document1->setRight(88)
            ->shouldBeCalled();
        $document1->setTop(77)
            ->shouldBeCalled();
        $document1->setBottom(66)
            ->shouldBeCalled();
        $document1->setPageBreak(55)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($data);
    }

    public function it_can_create_new_document_for_older_shopware(
        EntityManagerInterface $entityManager,
        ObjectRepository $documentsRepository,
        Document $document1
    ) {
        if (true === \method_exists($document1->getWrappedObject(), 'getKey')) {
            return;
        }

        $data = [
            'documentOne' => [
                'name'      => 'documentOne',
                'template'  => 'doc1.tpl',
                'numbers'   => 'doc01',
                'left'      => 99,
                'right'     => 88,
                'top'       => 77,
                'bottom'    => 66,
                'pagebreak' => 55,
            ],
        ];

        $documentsRepository->findOneBy(['name' => 'documentOne'])
            ->willReturn(null);

        $persistedDocument = null;
        $entityManager->persist(Argument::that(function ($document) use (&$persistedDocument) {
            if (false === \is_a($document, Document::class)) {
                return false;
            }

            $persistedDocument = $document;
            return true;
        }))
            ->shouldBeCalled();
        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($data);

        Assert::eq($persistedDocument->getName(), 'documentOne');
        Assert::eq($persistedDocument->getTemplate(), 'doc1.tpl');
        Assert::eq($persistedDocument->getNumbers(), 'doc01');
        Assert::eq($persistedDocument->getLeft(), 99);
        Assert::eq($persistedDocument->getRight(), 88);
        Assert::eq($persistedDocument->getTop(), 77);
        Assert::eq($persistedDocument->getBottom(), 66);
        Assert::eq($persistedDocument->getPageBreak(), 55);
    }
}
