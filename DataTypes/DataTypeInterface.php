<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
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
