<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Dumper;

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
