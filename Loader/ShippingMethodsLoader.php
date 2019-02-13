<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Dispatch\Dispatch;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ShippingMethodsLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ObjectRepository */
    private $shippingMethodsRepository;

    /** @var DenormalizerInterface */
    private $denormalizer;

    public function __construct(EntityManagerInterface $entityManager, DenormalizerInterface $denormalizer)
    {
        $this->entityManager = $entityManager;
        $this->shippingMethodsRepository = $entityManager->getRepository(Dispatch::class);
        $this->denormalizer = $denormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        foreach ($config as $id => $shippingMethodData) {
            try {
                $this->importShippingMethod($id, $shippingMethodData);
            } catch (\Throwable $throwable) {
                echo 'Error during import of shipping method ' . $id . PHP_EOL;
                echo $throwable->getMessage();
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param int   $shippingMethodId
     * @param array $shippingMethodData
     */
    private function importShippingMethod($shippingMethodId, $shippingMethodData)
    {
        $shippingMethod = $this->shippingMethodsRepository->find($shippingMethodId);
        if (null === $shippingMethod) {
            $shippingMethod = new Dispatch();
            $this->entityManager->persist($shippingMethod);
        }

        $this->denormalizer->denormalize($shippingMethodData, Dispatch::class, null, ['object_to_populate' => $shippingMethod]);
    }
}
