<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Dumper;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Dispatch\Dispatch;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShippingMethodsDumper implements DumperInterface
{
    /** @var ObjectRepository */
    private $shippingMethodsRepository;

    /** @var NormalizerInterface */
    private $normalizer;

    public function __construct(EntityManagerInterface $entityManager, NormalizerInterface $normalizer)
    {
        $this->shippingMethodsRepository = $entityManager->getRepository(Dispatch::class);
        $this->normalizer = $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $shippingMethods = [];
        foreach ($this->shippingMethodsRepository->findAll() as $shippingMethod) {
            $shippingMethods[$shippingMethod->getId()] = $this->normalizer->normalize($shippingMethod);
        }

        return $shippingMethods;
    }
}
