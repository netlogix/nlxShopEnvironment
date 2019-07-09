<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Dumper;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Search\CustomFacet;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FacetDumper implements DumperInterface
{
    /** @var ObjectRepository */
    private $facetRepository;

    /** @var NormalizerInterface */
    private $normalizer;

    public function __construct(
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer
    ) {
        $this->facetRepository = $entityManager->getRepository(CustomFacet::class);
        $this->normalizer = $normalizer;
    }

    public function dump()
    {
        $facets = [];
        foreach ($this->facetRepository->findAll() as $facet) {
            $facets[$facet->getName()] = $this->normalizer->normalize($facet);
        }

        return $facets;
    }
}
