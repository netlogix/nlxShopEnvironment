<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Serializer\Normalizer;

use Shopware\Models\Country\Country;
use Shopware\Models\Dispatch\Dispatch;
use Shopware\Models\Dispatch\ShippingCost;
use Shopware\Models\Payment\Payment;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class DispatchNormalizer extends ObjectNormalizer
{
    /** @var string[] */
    public const DISPATCH_PAYMENT_IGNORED_ATTRIBUTES = ['id', 'categories', 'holidays', 'attribute'];

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = self::DISPATCH_PAYMENT_IGNORED_ATTRIBUTES;

        return parent::denormalize($data, $type, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = self::DISPATCH_PAYMENT_IGNORED_ATTRIBUTES;

        return parent::normalize($object, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeValue($object, $attribute, $value, $format = null, array $context = [])
    {
        $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = self::DISPATCH_PAYMENT_IGNORED_ATTRIBUTES;

        if ($this->serializer instanceof DenormalizerInterface) {
            switch ($attribute) {
                case 'countries':
                    $value = $this->serializer->denormalize($value, Country::class . '[]', $format, $context);
                    break;

                case 'costsMatrix':
                    $value = $this->serializer->denormalize($value, ShippingCost::class . '[]', $format, $context);
                    break;

                case 'payments':
                    $value = $this->serializer->denormalize($value, Payment::class . '[]', $format, $context);
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
