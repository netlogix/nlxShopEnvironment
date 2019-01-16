<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Dumper;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use sdShopEnvironment\Dumper\DumperInterface;
use sdShopEnvironment\Dumper\PaymentMethodsDumper;
use Shopware\Models\Payment\Payment;

class PaymentMethodsDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ObjectRepository $paymentMethodsRepository
    ) {
        $entityManager
            ->getRepository(Payment::class)
            ->willReturn($paymentMethodsRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PaymentMethodsDumper::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_empty_payment_methods(ObjectRepository $paymentMethodsRepository)
    {
        $paymentMethodsRepository
            ->findAll()
            ->shouldBeCalled()
            ->willReturn([]);

        $this
            ->dump()
            ->shouldBe([]);
    }
}
