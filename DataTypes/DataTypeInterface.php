<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
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
