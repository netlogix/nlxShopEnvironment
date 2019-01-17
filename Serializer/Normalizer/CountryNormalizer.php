<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Serializer\Normalizer;

use Shopware\Models\Country\Country;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CountryNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /* @var Country $object */
        return $object->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Country;
    }
}
