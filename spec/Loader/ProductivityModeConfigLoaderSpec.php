<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Loader\LoaderInterface;
use nlxShopEnvironment\Loader\ProductivityModeConfigLoader;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Bundle\PluginInstallerBundle\Service\InstallerService;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Plugin\Plugin;

class ProductivityModeConfigLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        InstallerService $installerService,
        ModelRepository $objectRepository,
        Plugin $plugin
    ): void {
        $this->beConstructedWith(
            $entityManager,
            $installerService
        );

        $entityManager
            ->getRepository(Plugin::class)
            ->willReturn($objectRepository);

        $objectRepository
            ->findOneBy(Argument::exact(['name' => 'HttpCache']))
            ->willReturn($plugin);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductivityModeConfigLoader::class);
    }

    public function it_implements_loader_interface(): void
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_can_load_install_and_activate(
        InstallerService $installerService,
        Plugin $plugin
    ): void {
        $plugin
            ->getInstalled()
            ->willReturn(false);
        $plugin
            ->getActive()
            ->willReturn(false);

        $installerService
            ->installPlugin($plugin)
            ->shouldBeCalled();
        $installerService
            ->activatePlugin($plugin)
            ->shouldBeCalled();
        $installerService
            ->deactivatePlugin($plugin)
            ->shouldNotBeCalled();

        $this->load(['productive_mode' => true]);
    }

    public function it_can_load_and_activate(
        InstallerService $installerService,
        Plugin $plugin
    ): void {
        $plugin
            ->getInstalled()
            ->willReturn(true);
        $plugin
            ->getActive()
            ->willReturn(false);

        $installerService
            ->installPlugin($plugin)
            ->shouldNotBeCalled();
        $installerService
            ->activatePlugin($plugin)
            ->shouldBeCalled();
        $installerService
            ->deactivatePlugin($plugin)
            ->shouldNotBeCalled();

        $this->load(['productive_mode' => true]);
    }

    public function it_can_load_and_leave_activated(
        InstallerService $installerService,
        Plugin $plugin
    ): void {
        $plugin
            ->getInstalled()
            ->willReturn(true);
        $plugin
            ->getActive()
            ->willReturn(true);

        $installerService
            ->installPlugin($plugin)
            ->shouldNotBeCalled();
        $installerService
            ->activatePlugin($plugin)
            ->shouldNotBeCalled();
        $installerService
            ->deactivatePlugin($plugin)
            ->shouldNotBeCalled();

        $this->load(['productive_mode' => true]);
    }

    public function it_can_load_and_deactivate(
        InstallerService $installerService,
        Plugin $plugin
    ): void {
        $plugin
            ->getInstalled()
            ->willReturn(true);
        $plugin
            ->getActive()
            ->willReturn(true);

        $installerService
            ->installPlugin($plugin)
            ->shouldNotBeCalled();
        $installerService
            ->activatePlugin($plugin)
            ->shouldNotBeCalled();
        $installerService
            ->deactivatePlugin($plugin)
            ->shouldBeCalled();

        $this->load(['productive_mode' => false]);
    }

    public function it_can_load_and_leave_inactive(
        InstallerService $installerService,
        Plugin $plugin
    ): void {
        $plugin
            ->getInstalled()
            ->willReturn(true);
        $plugin
            ->getActive()
            ->willReturn(false);

        $installerService
            ->installPlugin($plugin)
            ->shouldNotBeCalled();
        $installerService
            ->activatePlugin($plugin)
            ->shouldNotBeCalled();
        $installerService
            ->deactivatePlugin($plugin)
            ->shouldNotBeCalled();

        $this->load(['productive_mode' => false]);
    }
}
