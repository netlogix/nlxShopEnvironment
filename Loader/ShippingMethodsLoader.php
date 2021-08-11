<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Dispatch\Dispatch;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ShippingMethodsLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ModelRepository */
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
    public function load(array $config): void
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

    private function outputException(int $id, \Exception $exception): void
    {
        if (!\defined('PHPSPEC')) {
            echo 'Error during import of shipping method ' . $id . PHP_EOL;
            echo $exception->getMessage();
        }
    }

    /**
     * @param mixed[] $shippingMethodData
     */
    private function importShippingMethod(int $shippingMethodId, array $shippingMethodData): void
    {
        $shippingMethod = $this->shippingMethodsRepository->find($shippingMethodId);
        if (null === $shippingMethod) {
            throw new \RuntimeException('The loading configuration contains a shipping method that is not yet created in the database. We cannot create such a shipping method! ShippingMethodId: ' . $shippingMethodId);
        }

        $this->denormalizer->denormalize($shippingMethodData, Dispatch::class, null, ['object_to_populate' => $shippingMethod]);
    }
}
