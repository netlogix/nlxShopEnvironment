<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use nlxShopEnvironment\Loader\LoaderInterface;
use nlxShopEnvironment\Loader\PaymentRulesLoader;
use PhpSpec\ObjectBehavior;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Payment\RuleSet;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PaymentRulesLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        DenormalizerInterface $denormalizer,
        ModelRepository $paymentRulesRepository,
        ClassMetadata $classMetadata
    ): void {
        $entityManager->getClassMetadata(RuleSet::class)
            ->willReturn($classMetadata);

        $this->beConstructedWith(
            $entityManager,
            $denormalizer,
            $paymentRulesRepository
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PaymentRulesLoader::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_can_load_new_payment_rules(
        EntityManagerInterface $entityManager,
        DenormalizerInterface $denormalizer,
        RuleSet $paymentRule
    ): void {
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
        ModelRepository $paymentRulesRepository,
        RuleSet $paymentRule
    ): void {
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
