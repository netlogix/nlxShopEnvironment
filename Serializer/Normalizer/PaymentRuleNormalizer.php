<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Serializer\Normalizer;

use Shopware\Models\Payment\Payment;
use Shopware\Models\Payment\RuleSet;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

class PaymentRuleNormalizer extends PropertyNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function setAttributeValue($object, $attribute, $value, $format = null, array $context = []): void
    {
        if ($this->serializer instanceof DenormalizerInterface) {
            if ('payment' === $attribute) {
                $value = $this->serializer->denormalize($value, Payment::class, $format);
                $object->setPaymentId($value->getId());
            }
        }

        parent::setAttributeValue($object, $attribute, $value, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return RuleSet::class === $type;
    }
}
