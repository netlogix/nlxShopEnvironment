<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Loader\FacetLoader;
use nlxShopEnvironment\Loader\LoaderInterface;
use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Search\CustomFacet;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class FacetLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ModelRepository $facetRepository,
        DenormalizerInterface $denormalizer
    ): void {
        if (!\class_exists('Shopware\Models\Search\CustomFacet')) {
            throw new SkippingException('Facets are not supported by this shopware version');
        }
        $entityManager
            ->getRepository(CustomFacet::class)
            ->willReturn($facetRepository);

        $this->beConstructedWith($entityManager, $denormalizer);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FacetLoader::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_aborts_if_it_is_an_unknown_id(
        EntityManagerInterface $entityManager,
        ModelRepository $facetRepository,
        DenormalizerInterface $denormalizer
    ): void {
        $facetRepository
            ->findOneBy(['name' => 'Preis'])
            ->willReturn(null);

        $data = ['Preis' => ['description' => 'hello world']];

        $denormalizer
            ->denormalize(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $entityManager
            ->flush()
            ->shouldBeCalled();

        $this->load($data);
    }

    public function it_can_load_existing_shipping_methods(
        EntityManagerInterface $entityManager,
        ModelRepository $facetRepository,
        DenormalizerInterface $denormalizer
    ): void {
        $facet = new CustomFacet();
        $facet->setName('Preis');

        $facetRepository
            ->findOneBy(['name' => 'Preis'])
            ->willReturn($facet);

        $data = ['Preis' => ['name' => 'schöne filter haben sie hier']];

        $denormalizer
            ->denormalize(
                Argument::any(),
                Argument::any(),
                Argument::any(),
                Argument::withEntry('object_to_populate', $facet->getWrappedObject())
            )
            ->shouldBeCalled();

        $entityManager
            ->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager
            ->flush()
            ->shouldBeCalled();

        $this->load($data);
    }
}
