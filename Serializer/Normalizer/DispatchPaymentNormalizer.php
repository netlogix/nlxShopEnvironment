<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Serializer\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Payment\Payment;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DispatchPaymentNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /** @var ModelRepository */
    private $paymentMethodsRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->paymentMethodsRepository = $entityManager->getRepository(Payment::class);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /* @var Payment $object */
        return $object->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Payment;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return $this->paymentMethodsRepository->findOneBy(['name' => $data]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return Payment::class === $type;
    }
}
