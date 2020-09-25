<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Serializer\Normalizer;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Shop\Shop;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShopNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /** @var ObjectRepository */
    private $shopRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->shopRepository = $entityManager->getRepository(Shop::class);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /* @var Shop $object */
        return $object->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Shop;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return $this->shopRepository->find($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return Shop::class === $type;
    }
}
