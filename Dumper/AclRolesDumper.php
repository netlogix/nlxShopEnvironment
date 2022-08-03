<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\User\Role;
use Shopware\Models\User\Rule;

class AclRolesDumper implements DumperInterface
{
    /** @var ModelRepository */
    private $roleRepository;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->roleRepository = $entityManager->getRepository(Role::class);
    }

    /**
     * {@inheritdoc}
     */
    public function dump(): array
    {
        $roleConfigs = [];

        /** @var Role[] $rules */
        $roles = $this->roleRepository->findAll();

        foreach ($roles as $role) {
            $roleName = $role->getName();

            $roleConfigs[$roleName]['admin'] = $role->getAdmin();
            $roleConfigs[$roleName]['enable'] = $role->getEnabled();

            $rules = $role->getRules();

            /** @var Rule $rule */
            foreach ($rules as $rule) {
                $resource = $rule->getResource();

                if (null === $resource) {
                    $roleConfigs[$roleName] = [];
                    continue;
                }
                $resourceName = $resource->getName();
                $privilege = $rule->getPrivilege();

                if ($privilege) {
                    $roleConfigs[$roleName]['acl'][$resourceName][] = $privilege->getName();
                }
            }
        }

        return $roleConfigs;
    }
}
