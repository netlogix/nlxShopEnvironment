<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Dumper\DumperInterface;
use nlxShopEnvironment\Dumper\FacetDumper;
use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Search\CustomFacet;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FacetDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ModelRepository $facetRepository,
        NormalizerInterface $normalizer
    ): void {
        if (!\class_exists('Shopware\Models\Search\CustomFacet')) {
            throw new SkippingException('Facets are not supported by this shopware version');
        }
        $entityManager
            ->getRepository(CustomFacet::class)
            ->willReturn($facetRepository);

        $this->beConstructedWith($entityManager, $normalizer);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FacetDumper::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_empty_facets(
        ModelRepository $facetRepository
    ): void {
        $facetRepository
            ->findAll()
            ->shouldBeCalled()
            ->willReturn([]);

        $this
            ->dump()
            ->shouldBe([]);
    }

    public function it_can_dump_facets(
        ModelRepository $facetRepository,
        NormalizerInterface $normalizer
    ): void {
        $facet1 = new CustomFacet();
        $facet2 = new CustomFacet();

        $facet1
            ->setName('Preis');

        $facet2
            ->setName('Farbe');

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
