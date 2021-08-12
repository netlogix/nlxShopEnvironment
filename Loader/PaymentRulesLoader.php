<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Payment\RuleSet;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PaymentRulesLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var DenormalizerInterface */
    private $denormalizer;

    /** @var ModelRepository */
    private $paymentRulesRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        DenormalizerInterface $denormalizer,
        ModelRepository $paymentRulesRepository
    ) {
        $this->entityManager = $entityManager;
        $this->denormalizer = $denormalizer;
        $this->paymentRulesRepository = $paymentRulesRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function load(?array $config): void
    {
        $paymentRules = $this->denormalizer->denormalize($config, RuleSet::class . '[]');

        if (true === empty($paymentRules)) {
            return;
        }

        /** @var RuleSet $paymentRule */
        foreach ($paymentRules as $paymentRule) {
            null === $this->paymentRulesRepository->find($paymentRule->getId())
                ? $this->entityManager->persist($paymentRule)
                : $this->entityManager->merge($paymentRule);
        }

        $metadata = $this->entityManager->getClassMetaData(RuleSet::class);
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());

        $this->entityManager->flush();
    }
}
