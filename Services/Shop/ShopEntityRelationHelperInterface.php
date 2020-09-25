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
    public function isRelationField($entityName);

    public function getEntity($entityName, $value): ModelEntity;
}
