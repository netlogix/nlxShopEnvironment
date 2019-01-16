<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Shop\Shop;

class PaymentMethodsLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ObjectRepository */
    private $paymentMethodsRepository;

    /** @var ObjectRepository */
    private $shopRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->paymentMethodsRepository = $entityManager->getRepository(Payment::class);
        $this->shopRepository = $entityManager->getRepository(Shop::class);
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

        $paymentMethodData['shops'] = $this->shopRepository->findBy(['id' => $paymentMethodData['shops']]);
        $paymentMethod->fromArray($paymentMethodData);
    }
}
