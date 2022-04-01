<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Services\AclRoles;

use \Shopware\Models\User\Repository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Factory\AclRole\AuthRoleFactoryInterface;
use nlxShopEnvironment\Factory\AclRole\RuleFactoryInterface;
use nlxShopEnvironment\Services\AclRoles\AclRolesImporter;
use nlxShopEnvironment\Services\AclRoles\AclRolesImporterInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\User\Privilege;
use Shopware\Models\User\Resource;
use Shopware\Models\User\Role;
use Shopware\Models\User\Rule;
use Shopware\Models\User\User;

class AclRolesImporterSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        AuthRoleFactoryInterface $authRoleFactory,
        RuleFactoryInterface $ruleFactory,
        ModelRepository $roleRepository,
        ModelRepository $resourceRepository,
        ModelRepository $privilegeRepository,
        Repository $userRepository
    ): void {
        $entityManager->getRepository(Role::class)
            ->willReturn($roleRepository);
        $entityManager->getRepository(Resource::class)
            ->willReturn($resourceRepository);
        $entityManager->getRepository(Privilege::class)
            ->willReturn($privilegeRepository);
        $entityManager->getRepository(User::class)
            ->willReturn($userRepository);

        $this->beConstructedWith(
            $entityManager,
            $authRoleFactory,
            $ruleFactory
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AclRolesImporter::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(AclRolesImporterInterface::class);
    }

    public function it_should_import_with_existing_role(
        EntityManagerInterface $entityManager,
        AuthRoleFactoryInterface $authRoleFactory,
        RuleFactoryInterface $ruleFactory,
        ModelRepository $roleRepository,
        ModelRepository $resourceRepository,
        ModelRepository $privilegeRepository,
        Repository $userRepository,
        AbstractQuery $query,
        Role $role,
        Rule $rule,
        Resource $resource,
        Privilege $privilege1,
        Privilege $privilege2
    ): void {
        $name = 'test_role';
        $data = $this->getData();

        $roleRepository->findOneBy(['name' => $name])
            ->willReturn($role);

        $role->getId()
            ->willReturn(1);

        $userRepository->getRuleDeleteByRoleIdQuery(1)
            ->willReturn($query);
        $query->execute()
            ->shouldBeCalled();

        $ruleFactory->create()
            ->willReturn($rule);

        $resourceRepository->findOneBy(['name' => 'emotion'])
            ->willReturn($resource);

        $privilegeRepository->findOneBy(['resource' => $resource, 'name' => 'create'])
            ->willReturn($privilege1);
        $privilegeRepository->findOneBy(['resource' => $resource, 'name' => 'read'])
            ->willReturn($privilege2);

        $rule->setRole($role);
        $rule->setResource($resource);
        $rule->setPrivilege($privilege1);
        $rule->setPrivilege($privilege2);

        $entityManager->persist($rule)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $authRoleFactory->create(Argument::any())
            ->shouldNotBeCalled();

        $this->import($name, $data);
    }

    public function it_should_import_with_not_existing_role(
        EntityManagerInterface $entityManager,
        AuthRoleFactoryInterface $authRoleFactory,
        RuleFactoryInterface $ruleFactory,
        ModelRepository $roleRepository,
        ModelRepository $resourceRepository,
        ModelRepository $privilegeRepository,
        Repository $userRepository,
        Role $role,
        Rule $rule,
        Resource $resource,
        Privilege $privilege1,
        Privilege $privilege2
    ): void {
        $name = 'test_role';
        $data = $this->getData();
        $data['name'] = $name;

        $roleRepository->findOneBy(['name' => $name])
            ->willReturn(null);

        $authRoleFactory->create($data)
            ->willReturn($role);

        $entityManager->persist($role)
            ->shouldBeCalled();

        $role->getId()
            ->willReturn(1);

        $ruleFactory->create()
            ->willReturn($rule);

        $resourceRepository->findOneBy(['name' => 'emotion'])
            ->willReturn($resource);

        $privilegeRepository->findOneBy(['resource' => $resource, 'name' => 'create'])
            ->willReturn($privilege1);
        $privilegeRepository->findOneBy(['resource' => $resource, 'name' => 'read'])
            ->willReturn($privilege2);

        $rule->setRole($role);
        $rule->setResource($resource);
        $rule->setPrivilege($privilege1);
        $rule->setPrivilege($privilege2);

        $entityManager->persist($rule)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $userRepository->getRuleDeleteByRoleIdQuery(Argument::any())
            ->shouldNotBeCalled();

        $this->import($name, $data);
    }

    public function it_should_throw_exception_if_resource_not_exists(
        EntityManagerInterface $entityManager,
        AuthRoleFactoryInterface $authRoleFactory,
        RuleFactoryInterface $ruleFactory,
        ModelRepository $roleRepository,
        ModelRepository $resourceRepository,
        ModelRepository $privilegeRepository,
        Repository $userRepository,
        Role $role
    ): void {
        $name = 'test_role';
        $data = $this->getData();
        $data['name'] = $name;

        $roleRepository->findOneBy(['name' => $name])
            ->willReturn(null);

        $authRoleFactory->create($data)
            ->willReturn($role);

        $entityManager->persist($role)
            ->shouldBeCalled();

        $role->getId()
            ->willReturn(1);

        $resourceRepository->findOneBy(['name' => 'emotion'])
            ->willReturn(null);

        $userRepository->getRuleDeleteByRoleIdQuery(Argument::any())
            ->shouldNotBeCalled();
        $privilegeRepository->findOneBy(Argument::any())
            ->shouldNotBeCalled();
        $entityManager->flush()
            ->shouldNotBeCalled();
        $ruleFactory->create()
            ->shouldNotBeCalled();

        $this->shouldThrow(\RuntimeException::class)
            ->during('import', [$name, $data]);
    }

    public function it_should_throw_exception_if_privilege_not_exists(
        EntityManagerInterface $entityManager,
        AuthRoleFactoryInterface $authRoleFactory,
        RuleFactoryInterface $ruleFactory,
        ModelRepository $roleRepository,
        ModelRepository $resourceRepository,
        ModelRepository $privilegeRepository,
        Repository $userRepository,
        Role $role,
        Resource $resource
    ): void {
        $name = 'test_role';
        $data = $this->getData();
        $data['name'] = $name;

        $roleRepository->findOneBy(['name' => $name])
            ->willReturn(null);

        $authRoleFactory->create($data)
            ->willReturn($role);

        $entityManager->persist($role)
            ->shouldBeCalled();

        $role->getId()
            ->willReturn(1);

        $resourceRepository->findOneBy(['name' => 'emotion'])
            ->willReturn($resource);

        $resource->getName()
            ->willReturn('emotion');

        $privilegeRepository->findOneBy(['resource' => $resource, 'name' => 'create'])
            ->willReturn(null);

        $userRepository->getRuleDeleteByRoleIdQuery(Argument::any())
            ->shouldNotBeCalled();
        $entityManager->flush()
            ->shouldNotBeCalled();
        $ruleFactory->create()
            ->shouldNotBeCalled();

        $this->shouldThrow(\RuntimeException::class)
            ->during('import', [$name, $data]);
    }

    /**
     * @return mixed[]
     */
    private function getData(): array
    {
        return [
            'admin' => false,
            'enable' => true,
            'acl' => [
                'emotion' => [
                    'create',
                    'read',
                ],
            ],
        ];
    }
}
