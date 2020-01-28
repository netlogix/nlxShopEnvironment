<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sdShopEnvironment\Factory;

class ReflectionClassFactory implements ReflectionClassFactoryInterface
{
    public function create($class): \ReflectionClass
    {
        return new \ReflectionClass($class);
    }
}
