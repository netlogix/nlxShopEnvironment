<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Dumper;

use Shopware\Components\Model\ModelRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaymentRulesDumper implements DumperInterface
{
    /** @var ModelRepository */
    private $paymentRulesRepository;

    /** @var NormalizerInterface */
    private $serializer;

    public function __construct(
        ModelRepository $paymentRulesRepository,
        NormalizerInterface $serializer
    ) {
        $this->paymentRulesRepository = $paymentRulesRepository;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(): array
    {
        $paymentRules = $this->paymentRulesRepository->findAll();
        return $this->serializer->normalize($paymentRules);
    }
}
