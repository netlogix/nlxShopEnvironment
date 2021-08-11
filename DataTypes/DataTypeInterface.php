<?php declare(strict_types=1);

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
    public function getDumper(): DumperInterface;

    public function getLoader(): LoaderInterface;
}
