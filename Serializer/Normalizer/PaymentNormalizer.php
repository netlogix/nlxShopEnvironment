<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Serializer\Normalizer;

use Doctrine\ORM\EntityManager;
use Shopware\Models\Country\Country;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Shop\Shop;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PaymentNormalizer extends ObjectNormalizer
{
    /** @var EntityManager */
    private $entityManager;

    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $data['shops'] =  $this->entityManager->getRepository(Shop::class)->findBy(['id' => $data['shops']]);
        $data['countries'] = $this->entityManager->getRepository(Country::class)->findBy(['id' => $data['countries']]);
        $data = parent::denormalize($data, $class, $format, $context);
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return Payment::class === $type;
    }
}
