<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Dumper\AclRolesDumper;
use nlxShopEnvironment\Dumper\DumperInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\User\Privilege;
use Shopware\Models\User\Resource;
use Shopware\Models\User\Role;
use Shopware\Models\User\Rule;

class AclRolesDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ModelRepository $roleRepository
    ): void {
        $entityManager
            ->getRepository(Role::class)
            ->willReturn($roleRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AclRolesDumper::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump(
        ModelRepository $roleRepository,
        Role $role,
        Rule $rule1,
        Rule $rule2,
        Resource $resource1,
        Resource $resource2,
        Privilege $privilege1,
        Privilege $privilege2
    ): void {
        $expected = [
            'test_role' => [
                'admin' => false,
                'enable' => true,
                'acl' => [
                    'emotion' => [
                        'create',
                        'read',
                    ],
                ],
            ],
        ];

        $roleRepository->findAll()
            ->willReturn([$role]);

        $role->getName()
            ->willReturn('test_role');
        $role->getAdmin()
            ->willReturn(false);
        $role->getEnabled()
            ->willReturn(true);
        $role->getRules()
            ->willReturn([$rule1, $rule2]);

        $rule1->getResource()
            ->willReturn($resource1);
        $rule2->getResource()
            ->willReturn($resource2);

        $resource1->getName()
            ->willReturn('emotion');
        $resource2->getName()
            ->willReturn('emotion');

        $rule1->getPrivilege()
            ->willReturn($privilege1);
        $rule2->getPrivilege()
            ->willReturn($privilege2);

        $privilege1->getName()
            ->willReturn('create');
        $privilege2->getName()
            ->willReturn('read');

        $this->dump()
            ->shouldBe($expected);
    }

    public function it_can_dump_empty_roles(
        ModelRepository $roleRepository
    ): void {
        $roleRepository->findAll()
            ->willReturn([]);

        $this->dump()
            ->shouldBe([]);
    }
}
