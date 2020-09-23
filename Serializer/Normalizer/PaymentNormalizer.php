<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Serializer\Normalizer;

use Shopware\Models\Country\Country;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Shop\Shop;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PaymentNormalizer extends ObjectNormalizer
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

                case 'shops':
                    $value = $this->serializer->denormalize($value, Shop::class . '[]', $format);
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
        return Payment::class === $type;
    }
}
