<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Serializer\Normalizer;

use Shopware\Models\Payment\Payment;
use Shopware\Models\Payment\RuleSet;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

class PaymentRuleNormalizer  extends PropertyNormalizer
{
    public function setAttributeValue($object, $attribute, $value, $format = null, array $context = [])
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
