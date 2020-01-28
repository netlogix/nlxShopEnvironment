<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sdShopEnvironment\DataTypes;

use sdShopEnvironment\Dumper\DumperInterface;
use sdShopEnvironment\Loader\LoaderInterface;

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
