<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Serializer;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Shopware\Components\Model\ModelEntity;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Shop\Shop;

class PaymentMethodSerializer implements SerializerInterface
{
    /** @var ObjectRepository */
    private $shopRepository;

    /** @var ClassMetadata */
    private $classMetadata;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->shopRepository = $entityManager->getRepository(Shop::class);
        $this->classMetadata = $entityManager->getClassMetadata(Payment::class);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(ModelEntity $entity)
    {
        $paymentData = [];

        foreach ($this->classMetadata->getFieldNames() as $fieldName) {
            switch ($fieldName) {
                case 'id':
                case 'name':
                    // ignore id due to missing setId method (necessary for reimporting payment method)
                    // Ignore name: used as identifier (is unique by sql scheme)
                    break;
                default:
                    $getter = 'get' . ucfirst($fieldName);
                    $data = $entity->$getter();
                    $paymentData[$fieldName] = $data;
                    break;
            }
        }

        $paymentData['shops'] = $entity->getShops()
            ->map(function (Shop $shop) {
                return $shop->getId();
            })
            ->toArray();

        return $paymentData;
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize(ModelEntity $targetEntity, $data)
    {
        $data['shops'] = $this->shopRepository->findBy(['id' => $data['shops']]);
        $targetEntity->fromArray($data);
        return $targetEntity;
    }
}
