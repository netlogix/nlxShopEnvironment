<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Serializer\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Serializer\Normalizer\CountryNormalizer;
use PhpSpec\ObjectBehavior;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Country\Country;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CountryNormalizerSpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $entityManager, ModelRepository $countryRepository): void
    {
        $entityManager
            ->getRepository(Country::class)
            ->willReturn($countryRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CountryNormalizer::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    public function it_supports_country_normalization(Country $country): void
    {
        $this->supportsNormalization($country)->shouldBe(true);
    }

    public function it_does_not_support_other_normalization(\stdClass $object): void
    {
        $this->supportsNormalization($object)->shouldBe(false);
    }

    public function it_supports_country_denormalization(): void
    {
        $this->supportsDenormalization([], Country::class)->shouldBe(true);
    }

    public function it_does_not_support_other_denormalization(): void
    {
        $this->supportsDenormalization([], \stdClass::class)->shouldBe(false);
    }

    public function it_returns_country_id_on_normalization(Country $country): void
    {
        $country
            ->getId()
            ->shouldBeCalled()
            ->willReturn(1);

        $this
            ->normalize($country)
            ->shouldBe(1);
    }

    public function it_returns_country_on_denormalization(
        Country $country,
        ModelRepository $countryRepository
    ): void {
        $data = 12;

        $countryRepository
            ->find(12)
            ->shouldBeCalled()
            ->willReturn($country);

        $this
            ->denormalize($data, Country::class)
            ->shouldBe($country);
    }
}
