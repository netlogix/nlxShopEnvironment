<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Dumper;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Dumper\DumperInterface;
use sdShopEnvironment\Dumper\FacetDumper;
use Shopware\Models\Search\CustomFacet;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FacetDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ObjectRepository $facetRepository,
        NormalizerInterface $normalizer
    ) {
        if (!\class_exists('CustomFacet')) {
            throw new SkippingException('Facets are not supported by this shopware version');
        }
        $entityManager
            ->getRepository(CustomFacet::class)
            ->willReturn($facetRepository);

        $this->beConstructedWith($entityManager, $normalizer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(FacetDumper::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_empty_facets(
        ObjectRepository $facetRepository
    ) {
        $facetRepository
            ->findAll()
            ->shouldBeCalled()
            ->willReturn([]);

        $this
            ->dump()
            ->shouldBe([]);
    }

    public function it_can_dump_facets(
        ObjectRepository $facetRepository,
        NormalizerInterface $normalizer
    ) {
        $facet1 = new CustomFacet();
        $facet2 = new CustomFacet();
        $facet1
            ->getName()
            ->willReturn('Preis');

        $facet2
            ->getName()
            ->willReturn('Farbe');

        $facetRepository
            ->findAll()
            ->shouldBeCalled()
            ->willReturn([$facet1, $facet2]);

        $normalizer
            ->normalize(Argument::type(CustomFacet::class))
            ->shouldBeCalledTimes(2)
            ->willReturn(['data' => 'data']);

        $this
            ->dump()
            ->shouldBeLike([
                'Preis' => ['data' => 'data'],
                'Farbe' => ['data' => 'data'],
            ]);
    }
}
