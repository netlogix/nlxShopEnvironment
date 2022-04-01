<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Services\AclRoles;

interface AclRolesImporterInterface
{
    /**
     * @param mixed[] $data
     */
    public function import(string $name, array $data): void;
}
