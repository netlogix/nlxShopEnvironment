<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Serializer\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Dispatch\ShippingCost;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShippingCostNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /** @var AbstractNormalizer */
    private $normalizer;

    /** @var ModelRepository */
    private $shippingCostRepository;

    public function __construct(AbstractNormalizer $normalizer, EntityManagerInterface $entityManager)
    {
        $this->normalizer = $normalizer;
        $this->shippingCostRepository = $entityManager->getRepository(ShippingCost::class);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = [
            'from'  => $object->getFrom(),
            'value' => $object->getValue(),
            'factor' => $object->getFactor(),
        ];
        $dispatchObject = $object->getDispatch();
        if (null !== $dispatchObject) {
            $data['dispatch'] = $dispatchObject->getId();
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ShippingCost;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $shippingCost = $this->shippingCostRepository->findOneBy(
            [
                'from'     => $data['from'],
                'dispatch' => $data['dispatch'],
            ]
        );
        if (null === $shippingCost) {
            $shippingCost = new ShippingCost();
        }

        return $this->normalizer->denormalize($data, $class, $format, [
                AbstractNormalizer::OBJECT_TO_POPULATE => $shippingCost,
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['dispatch'],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return ShippingCost::class === $type;
    }
}
