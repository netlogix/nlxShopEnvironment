<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Factory;

class ReflectionClassFactory implements ReflectionClassFactoryInterface
{
    public function create($class): \ReflectionClass
    {
        return new \ReflectionClass($class);
    }
}
