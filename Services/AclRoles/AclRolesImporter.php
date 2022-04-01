<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Services\AclRoles;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Factory\AclRole\AuthRoleFactoryInterface;
use nlxShopEnvironment\Factory\AclRole\RuleFactoryInterface;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\User\Privilege;
use Shopware\Models\User\Resource;
use Shopware\Models\User\Role;
use Shopware\Models\User\User;

class AclRolesImporter implements AclRolesImporterInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var AuthRoleFactoryInterface */
    private $authRoleFactory;

    /** @var RuleFactoryInterface */
    private $ruleFactory;

    /** @var ModelRepository */
    private $roleRepository;

    /** @var ModelRepository */
    private $resourceRepository;

    /** @var ModelRepository */
    private $privilegeRepository;

    /** @var \Shopware\Models\User\Repository */
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        AuthRoleFactoryInterface $authRoleFactory,
        RuleFactoryInterface $ruleFactory
    ) {
        $this->entityManager = $entityManager;
        $this->authRoleFactory = $authRoleFactory;
        $this->ruleFactory = $ruleFactory;

        $this->roleRepository = $this->entityManager->getRepository(Role::class);
        $this->resourceRepository = $this->entityManager->getRepository(Resource::class);
        $this->privilegeRepository = $this->entityManager->getRepository(Privilege::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    /**
     * {@inheritdoc}
     */
    public function import(string $name, array $data): void
    {
        $data['name'] = $name;
        $role = $this->getRole($data);
        $acl = $data['acl'] ?? [];

        foreach ($acl as $resourceName => $privileges) {
            foreach ($privileges as $privilegeName) {
                $resource = $this->getResource($resourceName);
                $privilege = $this->getPrivilege($resource, $privilegeName);
                $rule = $this->ruleFactory->create();

                $rule->setRole($role);
                $rule->setResource($resource);
                $rule->setPrivilege($privilege);

                $this->entityManager->persist($rule);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param mixed[] $data
     */
    private function getRole(array $data): Role
    {
        $role = $this->roleRepository->findOneBy(['name' => $data['name']]);

        if (null !== $role) {
            $this->clearOldRules($role);
            return $role;
        }
        $role =  $this->authRoleFactory->create($data);
        $this->entityManager->persist($role);

        return $role;
    }

    private function clearOldRules(Role $role): void
    {
        $query = $this->userRepository->getRuleDeleteByRoleIdQuery($role->getId());
        $query->execute();
    }

    private function getResource(string $resourceName): Resource
    {
        $resource = $this->resourceRepository->findOneBy(['name' => $resourceName]);

        if (null === $resource) {
            throw new \RuntimeException(\sprintf(
                'Resource with the name %s not exists',
                $resourceName
            ));
        }
        return $resource;
    }

    private function getPrivilege(Resource $resource, string $privilegeName): Privilege
    {
        $privilege = $this->privilegeRepository->findOneBy(['resource' => $resource, 'name' => $privilegeName]);

        if (null === $privilege) {
            throw new \RuntimeException(\sprintf(
                'Privilege with the resource %s and privilege name %s not exists',
                $resource->getName(),
                $privilegeName
            ));
        }
        return $privilege;
    }
}
