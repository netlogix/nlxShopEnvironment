<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Payment\Payment;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaymentMethodsDumper implements DumperInterface
{
    /** @var ModelRepository */
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
    public function dump(): array
    {
        $paymentMethods = [];

        foreach ($this->paymentMethodsRepository->findAll() as $paymentMethod) {
            $paymentMethods[$paymentMethod->getName()] = $this->serializer->normalize($paymentMethod);
        }

        return $paymentMethods;
    }
}
