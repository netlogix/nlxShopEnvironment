<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Payment\Payment;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PaymentMethodsLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ObjectRepository */
    private $paymentMethodsRepository;

    /** @var DenormalizerInterface */
    private $denormalizer;

    public function __construct(EntityManagerInterface $entityManager, DenormalizerInterface $denormalizer)
    {
        $this->entityManager = $entityManager;
        $this->paymentMethodsRepository = $entityManager->getRepository(Payment::class);
        $this->denormalizer = $denormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        foreach ($config as $id => $paymentMethodData) {
            try {
                $this->importPaymentMethod($id, $paymentMethodData);
            } catch (\Throwable $throwable) {
                echo 'Error during import of payment method ' . $id . PHP_EOL;
                echo $throwable->getMessage();
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param string $paymentMethodName
     * @param array  $paymentMethodData
     */
    private function importPaymentMethod($paymentMethodName, $paymentMethodData)
    {
        $paymentMethod = $this->paymentMethodsRepository->findOneBy(['name' => $paymentMethodName]);
        if (null === $paymentMethod) {
            $paymentMethod = new Payment();
            $paymentMethod->setName($paymentMethodName);
            $this->entityManager->persist($paymentMethod);
        }

        $this->denormalizer->denormalize($paymentMethodData, Payment::class, null, ['object_to_populate' => $paymentMethod]);
    }
}
