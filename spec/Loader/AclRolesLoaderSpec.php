<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Loader;

use nlxShopEnvironment\Loader\AclRolesLoader;
use nlxShopEnvironment\Loader\LoaderInterface;
use nlxShopEnvironment\Services\AclRoles\AclRolesImporterInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AclRolesLoaderSpec extends ObjectBehavior
{
    public function let(
        AclRolesImporterInterface $aclRolesImporter
    ): void {
        $this->beConstructedWith($aclRolesImporter);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AclRolesLoader::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_should_load(AclRolesImporterInterface $aclRolesImporter): void
    {
        $config = [
            'test_role' => [
                'admin' => false,
                'enable' => true,
                'acl' => [],
            ],
        ];

        $aclRolesImporter->import('test_role', $config['test_role'])
            ->shouldBeCalled();

        $this->load($config);
    }

    public function it_should_do_nothing_if_config_is_null(AclRolesImporterInterface $aclRolesImporter): void
    {
        $aclRolesImporter->import(Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $this->load(null);
    }
}
