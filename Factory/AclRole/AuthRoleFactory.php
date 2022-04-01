<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Factory\AclRole;

use Shopware\Models\User\Role;

class AuthRoleFactory implements AuthRoleFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $data): Role
    {
        $role = new Role();
        $role->setName($data['name'] ?? '');
        $role->setAdmin((int) $data['admin'] ?? 0);
        $role->setEnabled((int) $data['enable'] ?? 0);
        $role->setDescription($data['description'] ?? '');
        $role->setSource('custom');

        return $role;
    }
}
