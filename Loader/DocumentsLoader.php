<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Document\Document;

class DocumentsLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config): void
    {
        $number = 0;
        foreach ($config as $key => $configElement) {
            $document = $this->findOrCreateDocument($key);
            $document->setName($configElement['name'] ?: '');
            $document->setTemplate($configElement['template'] ?: 'index.tpl');
            $document->setNumbers($configElement['numbers'] ?: $number);
            $document->setLeft($configElement['left'] ?: 25);
            $document->setRight($configElement['right'] ?: 10);
            $document->setTop($configElement['top'] ?: 20);
            $document->setBottom($configElement['bottom'] ?: 20);
            $document->setPageBreak($configElement['pagebreak'] ?: 10);
            ++$number;
        }

        $this->entityManager->flush();
    }

    private function findOrCreateDocument(string $key): Document
    {
        $keyPropertyAndSetter = $this->getKeyPropertyAndSetter();
        $keyProperty = $keyPropertyAndSetter['property'];
        $keySetter = $keyPropertyAndSetter['setter'];

        $repository = $this->entityManager->getRepository(Document::class);
        $document = $repository->findOneBy([$keyProperty => $key]);

        if (null === $document) {
            $document = new Document();
            $document->$keySetter($key);
            $this->entityManager->persist($document);
        }

        return $document;
    }

    /**
     * @return string[]
     */
    private function getKeyPropertyAndSetter(): array
    {
        $exampleDocument = new Document();

        if (\method_exists($exampleDocument, 'setKey')) {
            return [
                'property' => 'key',
                'setter'   => 'setKey',
            ];
        } else {
            return [
                'property' => 'name',
                'setter'   => 'setName',
            ];
        }
    }
}
