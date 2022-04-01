<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Factory\AclRole;

use Shopware\Models\User\Role;

interface AuthRoleFactoryInterface
{
    /**
     * @param mixed[] $data
     */
    public function create(array $data): Role;
}
