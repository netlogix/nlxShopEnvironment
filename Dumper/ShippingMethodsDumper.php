<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Dispatch\Dispatch;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShippingMethodsDumper implements DumperInterface
{
    /** @var ModelRepository */
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
