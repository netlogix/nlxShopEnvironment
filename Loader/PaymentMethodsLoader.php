<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Payment\Payment;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PaymentMethodsLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ModelRepository */
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
    public function load(array $config): void
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
     * @param mixed[] $paymentMethodData
     */
    private function importPaymentMethod(string $paymentMethodName, array $paymentMethodData): void
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
