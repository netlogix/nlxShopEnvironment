<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Serializer\Normalizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Serializer\Normalizer\CountryNormalizer;
use Shopware\Models\Country\Country;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CountryNormalizerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(CountryNormalizer::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    public function it_support_country_normalization(Country $country)
    {
        $this->supportsNormalization($country)->shouldBe(true);
    }

    public function it_does_not_support_other_normalization(\stdClass $object)
    {
        $this->supportsNormalization($object)->shouldBe(false);
    }

    public function it_returns_country_id_on_normalization(Country $country)
    {
        $country
            ->getId()
            ->shouldBeCalled()
            ->willReturn(1);

        $this
            ->normalize($country)
            ->shouldBe(1);
    }
}
