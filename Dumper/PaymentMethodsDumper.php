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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaymentMethodsDumper implements DumperInterface
{
    /** @var ObjectRepository */
    private $paymentMethodsRepository;

    /** @var NormalizerInterface */
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, NormalizerInterface $serializer)
    {
        $this->paymentMethodsRepository = $entityManager->getRepository(Payment::class);
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $paymentMethods = [];

        foreach ($this->paymentMethodsRepository->findAll() as $paymentMethod) {
            $paymentMethods[$paymentMethod->getName()] = $this->serializer->normalize($paymentMethod);
        }

        return $paymentMethods;
    }
}
