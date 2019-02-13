<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Serializer\Normalizer;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Dispatch\ShippingCost;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShippingCostNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /** @var AbstractNormalizer */
    private $normalizer;

    /** @var ObjectRepository */
    private $shippingCostRepository;

    public function __construct(AbstractNormalizer $normalizer, EntityManagerInterface $entityManager)
    {
        $this->normalizer = $normalizer;
        $this->normalizer->setIgnoredAttributes(['dispatch']);
        $this->shippingCostRepository = $entityManager->getRepository(ShippingCost::class);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return $this->normalizer->normalize($object, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ShippingCost;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $shippingCost = $this->shippingCostRepository->find($data['id']);
        if (null === $shippingCost) {
            $shippingCost = new ShippingCost();
        }

        return $this->normalizer->denormalize($data, $class, $format, ['object_to_populate' => $shippingCost]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return ShippingCost::class === $type;
    }
}
