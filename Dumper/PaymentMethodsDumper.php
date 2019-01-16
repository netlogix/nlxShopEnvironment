<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Dumper;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Shop\Shop;

class PaymentMethodsDumper implements DumperInterface
{
    /** @var ObjectRepository */
    private $paymentMethodsRepository;

    /** @var ClassMetadata */
    private $classMetadata;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->paymentMethodsRepository = $entityManager->getRepository(Payment::class);
        $this->classMetadata = $entityManager->getClassMetadata(Payment::class);
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $paymentMethods = [];

        foreach ($this->paymentMethodsRepository->findAll() as $paymentMethod) {
            $paymentMethods[$paymentMethod->getId()] = $this->getPaymentMethodArrayData($paymentMethod);
        }

        return $paymentMethods;
    }

    /**
     * @return string[]
     */
    private function getPaymentMethodArrayData(Payment $paymentMethod)
    {
        $paymentData = [];

        foreach ($this->classMetadata->getFieldNames() as $fieldName) {
            switch ($fieldName) {
                case 'id':
                    // id is already array key
                    break;
                default:
                    $getter = 'get' . ucfirst($fieldName);
                    $data = $paymentMethod->$getter();
                    $paymentData[$fieldName] = $data;
                    break;
            }
        }

        $paymentData['shops'] = $paymentMethod->getShops()
            ->map(function (Shop $shop) {
                return $shop->getId();
            })
            ->toArray();

        return $paymentData;
    }
}
