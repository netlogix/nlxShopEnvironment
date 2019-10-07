<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Services\Shop;

use Shopware\Components\Model\ModelEntity;

interface ShopEntityRelationHelperInterface
{
    public function isRelationField($entityName);

    public function getEntity($entityName, $value): ModelEntity;
}
