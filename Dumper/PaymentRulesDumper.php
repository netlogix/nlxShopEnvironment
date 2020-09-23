<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Dumper;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaymentRulesDumper implements DumperInterface
{
    /** @var ObjectRepository */
    private $paymentRulesRepository;

    /** @var NormalizerInterface */
    private $serializer;

    public function __construct(
        ObjectRepository $paymentRulesRepository,
        NormalizerInterface $serializer
    ) {
        $this->paymentRulesRepository = $paymentRulesRepository;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $paymentRules = $this->paymentRulesRepository->findAll();
        return $this->serializer->normalize($paymentRules);
    }
}
