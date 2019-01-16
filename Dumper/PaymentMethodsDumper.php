<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Dumper;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Payment\Payment;

class PaymentMethodsDumper implements DumperInterface
{
    /** @var ObjectRepository */
    private $paymentMethodsRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->paymentMethodsRepository = $entityManager->getRepository(Payment::class);
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $paymentMethods = [];
        $this->paymentMethodsRepository->findAll();
        return $paymentMethods;
    }
}
