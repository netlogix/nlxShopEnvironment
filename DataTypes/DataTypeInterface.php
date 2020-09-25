<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\DataTypes;

use nlxShopEnvironment\Dumper\DumperInterface;
use nlxShopEnvironment\Loader\LoaderInterface;

interface DataTypeInterface
{
    /**
     * @return DumperInterface
     */
    public function getDumper();

    /**
     * @return LoaderInterface
     */
    public function getLoader();
}
