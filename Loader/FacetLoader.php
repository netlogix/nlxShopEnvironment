<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Search\CustomFacet;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class FacetLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ObjectRepository */
    private $facetRepository;

    /** @var DenormalizerInterface */
    private $denormalizer;

    public function __construct(
        EntityManagerInterface $entityManager,
        DenormalizerInterface $denormalizer
    ) {
        $this->entityManager = $entityManager;
        $this->facetRepository = $entityManager->getRepository(CustomFacet::class);
        $this->denormalizer = $denormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        foreach ($config as $facetName => $facetData) {
            try {
                $this->importFacet($facetName, $facetData);
            } catch (\Throwable $throwable) {
                echo 'Error during import of facet ' . $facetName . PHP_EOL;
                echo $throwable->getMessage();
                continue;
            } catch (\Exception $exception) {
                // PHP5.6 Support
                echo 'Error during import of facet ' . $facetName . PHP_EOL;
                echo $exception->getMessage();
                continue;
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param string $facetName
     * @param array  $facetData
     */
    private function importFacet($facetName, $facetData)
    {
        $facet = $this->facetRepository->findOneBy(['name' => $facetName]);
        if (null === $facet) {
            $facet = new CustomFacet();
            $this->entityManager->persist($facet);
        }
        $this->denormalizer->denormalize($facetData, CustomFacet::class, null, ['object_to_populate' => $facet]);
    }
}