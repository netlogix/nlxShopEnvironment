<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Factory\AclRole;

use Shopware\Models\User\Rule;

interface RuleFactoryInterface
{
    public function create(): Rule;
}
