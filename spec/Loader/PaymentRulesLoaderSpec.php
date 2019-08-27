<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use sdShopEnvironment\Loader\LoaderInterface;
use sdShopEnvironment\Loader\PaymentRulesLoader;
use Shopware\Models\Payment\RuleSet;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PaymentRulesLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        DenormalizerInterface $denormalizer,
        ObjectRepository $paymentRulesRepository,
        ClassMetadata $classMetadata
    ) {
        $entityManager->getClassMetadata(RuleSet::class)
            ->willReturn($classMetadata);

        $this->beConstructedWith(
            $entityManager,
            $denormalizer,
            $paymentRulesRepository
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PaymentRulesLoader::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_can_load_new_payment_rules(
        EntityManagerInterface $entityManager,
        DenormalizerInterface $denormalizer,
        RuleSet $paymentRule
    ) {
        $rawPaymentRules = [['id' => 2]];

        $denormalizer->denormalize($rawPaymentRules, RuleSet::class . '[]')
            ->willReturn([$paymentRule]);

        $entityManager->persist($paymentRule)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($rawPaymentRules);
    }

    public function it_can_update_an_existing_payment_rules(
        EntityManagerInterface $entityManager,
        DenormalizerInterface $denormalizer,
        ObjectRepository $paymentRulesRepository,
        RuleSet $paymentRule
    ) {
        $rawPaymentRules = [['id' => 2]];

        $denormalizer->denormalize($rawPaymentRules, RuleSet::class . '[]')
            ->willReturn([$paymentRule]);

        $paymentRule->getId()
            ->shouldBeCalled()
            ->willReturn(2);

        $paymentRulesRepository->find(2)
            ->shouldBeCalled()
            ->willReturn($paymentRule);

        $entityManager->merge($paymentRule)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($rawPaymentRules);
    }
}
