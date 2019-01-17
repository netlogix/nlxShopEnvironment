<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Serializer\Normalizer;

use Shopware\Models\Country\Country;
use Shopware\Models\Dispatch\Dispatch;
use Shopware\Models\Dispatch\ShippingCost;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class DispatchNormalizer extends ObjectNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function setAttributeValue($object, $attribute, $value, $format = null, array $context = [])
    {
        if ($this->serializer instanceof DenormalizerInterface) {
            switch ($attribute) {
                case 'countries':
                    $value = $this->serializer->denormalize($value, Country::class . '[]', $format);
                    break;

                case 'costsMatrix':
                    $value = $this->serializer->denormalize($value, ShippingCost::class . '[]', $format);
                    break;
            }
        }

        parent::setAttributeValue($object, $attribute, $value, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return Dispatch::class === $type;
    }
}
