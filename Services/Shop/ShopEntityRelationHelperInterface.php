<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Services\Shop;

use Shopware\Components\Model\ModelEntity;

interface ShopEntityRelationHelperInterface
{
    public function isRelationField(string $entityName): bool;

    public function getEntity(string $entityName, string $value): ModelEntity;
}
