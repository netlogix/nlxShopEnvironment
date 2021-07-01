<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Search\CustomFacet;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FacetDumper implements DumperInterface
{
    /** @var ModelRepository */
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
