<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\Process\ReRunner\PlatformSpecificReRunner;
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
                $this->outputException($id, $throwable);
                continue;
            } catch (\Exception $exception) {
                // PHP5.6 Support
                $this->outputException($id, $exception);
                continue;
            }
        }

        $this->entityManager->flush();
    }

    private function outputException(string $id, \Exception $exception)
    {
        if (!\defined('PHPSPEC')) {
            echo 'Error during import of shipping method ' . $id . PHP_EOL;
            echo $exception->getMessage();
        }
    }

    /**
     * @param int   $shippingMethodId
     * @param array $shippingMethodData
     */
    private function importShippingMethod($shippingMethodId, $shippingMethodData)
    {
        $shippingMethod = $this->shippingMethodsRepository->find($shippingMethodId);
        if (null === $shippingMethod) {
            throw new \RuntimeException('The loading configuration contains a shipping method that is not yet created in the database. We cannot create such a shipping method! ShippingMethodId: ' . $shippingMethodId);
        }

        $this->denormalizer->denormalize($shippingMethodData, Dispatch::class, null, ['object_to_populate' => $shippingMethod]);
    }
}
