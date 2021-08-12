<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Dumper\DumperInterface;
use nlxShopEnvironment\Dumper\ProductivityModeConfigDumper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Plugin\Plugin;

class ProductivityModeConfigDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager
    ): void {
        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductivityModeConfigDumper::class);
    }

    public function it_implements_dumper_interface(): void
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_positive(
        EntityManagerInterface $entityManager,
        ModelRepository $ModelRepository,
        Plugin $plugin
    ): void {
        $entityManager
            ->getRepository(Argument::exact(Plugin::class))
            ->willReturn($ModelRepository);

        $ModelRepository
            ->findOneBy(Argument::exact(['name' => 'HttpCache']))
            ->willReturn($plugin);

        $plugin
            ->getInstalled()
            ->willReturn(true);
        $plugin
            ->getActive()
            ->willReturn(true);

        $this
            ->dump()
            ->shouldReturn(['productive_mode' => true]);
    }

    public function it_can_dump_negative_if_uninstalled(
        EntityManagerInterface $entityManager,
        ModelRepository $modelRepository,
        Plugin $plugin
    ): void {
        $entityManager
            ->getRepository(Argument::exact(Plugin::class))
            ->willReturn($modelRepository);

        $modelRepository
            ->findOneBy(Argument::exact(['name' => 'HttpCache']))
            ->willReturn($plugin);

        $plugin
            ->getInstalled()
            ->willReturn(false);
        $plugin
            ->getActive()
            ->willReturn(true);

        $this
            ->dump()
            ->shouldReturn(['productive_mode' => false]);
    }

    public function it_can_dump_negative_if_inactive(
        EntityManagerInterface $entityManager,
        ModelRepository $modelRepository,
        Plugin $plugin
    ): void {
        $entityManager
            ->getRepository(Argument::exact(Plugin::class))
            ->willReturn($modelRepository);

        $modelRepository
            ->findOneBy(Argument::exact(['name' => 'HttpCache']))
            ->willReturn($plugin);

        $plugin
            ->getInstalled()
            ->willReturn(true);
        $plugin
            ->getActive()
            ->willReturn(false);

        $this
            ->dump()
            ->shouldReturn(['productive_mode' => false]);
    }

    public function it_can_dump_negative_if_inactive_and_uninstalled(
        EntityManagerInterface $entityManager,
        ModelRepository $modelRepository,
        Plugin $plugin
    ): void {
        $entityManager
            ->getRepository(Argument::exact(Plugin::class))
            ->willReturn($modelRepository);

        $modelRepository
            ->findOneBy(Argument::exact(['name' => 'HttpCache']))
            ->willReturn($plugin);

        $plugin
            ->getInstalled()
            ->willReturn(false);
        $plugin
            ->getActive()
            ->willReturn(false);

        $this
            ->dump()
            ->shouldReturn(['productive_mode' => false]);
    }
}
