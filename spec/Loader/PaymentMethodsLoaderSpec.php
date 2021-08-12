<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Loader\LoaderInterface;
use nlxShopEnvironment\Loader\PaymentMethodsLoader;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Payment\Payment;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PaymentMethodsLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ModelRepository $paymentMethodsRepository,
        DenormalizerInterface $denormalizer
    ): void {
        $entityManager
            ->getRepository(Payment::class)
            ->willReturn($paymentMethodsRepository);

        $this->beConstructedWith($entityManager, $denormalizer);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PaymentMethodsLoader::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_can_load_new_payment_methods(
        EntityManagerInterface $entityManager,
        ModelRepository $paymentMethodsRepository,
        DenormalizerInterface $denormalizer
    ): void {
        $paymentMethodsRepository
            ->findOneBy(['name' => 'new_payment_method'])
            ->willReturn(null);

        $data = ['new_payment_method' => ['description' => 'hello world']];

        $denormalizer
            ->denormalize(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->shouldBeCalled();

        $entityManager
            ->persist(Argument::type(Payment::class))
            ->shouldBeCalled();

        $entityManager
            ->flush()
            ->shouldBeCalled();

        $this->load($data);
    }

    public function it_can_load_existing_payment_methods(
        EntityManagerInterface $entityManager,
        ModelRepository $paymentMethodsRepository,
        Payment $paymentMethod,
        DenormalizerInterface $denormalizer
    ): void {
        $paymentMethodsRepository
            ->findOneBy(['name' => 'new_payment_method'])
            ->willReturn($paymentMethod);

        $data = ['new_payment_method' => ['description' => 'hello world', 'shops' => []]];

        $denormalizer
            ->denormalize(Argument::any(), Argument::any(), Argument::any(), Argument::withEntry('object_to_populate', $paymentMethod->getWrappedObject()))
            ->shouldBeCalled();

        $entityManager
            ->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager
            ->flush()
            ->shouldBeCalled();

        $this->load($data);
    }
}
