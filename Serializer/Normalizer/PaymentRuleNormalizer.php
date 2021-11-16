<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Serializer\Normalizer;

use Shopware\Models\Payment\Payment;
use Shopware\Models\Payment\RuleSet;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

class PaymentRuleNormalizer extends PropertyNormalizer
{
    /** @var string[] */
    public const PAYMENT_RULE_IGNORED_ATTRIBUTES = ['paymentId'];

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = self::PAYMENT_RULE_IGNORED_ATTRIBUTES;

        return parent::denormalize($data, $type, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = self::PAYMENT_RULE_IGNORED_ATTRIBUTES;

        return parent::normalize($object, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeValue($object, $attribute, $value, $format = null, array $context = []): void
    {
        $context[AbstractNormalizer::IGNORED_ATTRIBUTES] = self::PAYMENT_RULE_IGNORED_ATTRIBUTES;

        if ($this->serializer instanceof DenormalizerInterface) {
            if ('payment' === $attribute) {
                $value = $this->serializer->denormalize($value, Payment::class, $format, $context);
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
