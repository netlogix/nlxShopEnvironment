<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Factory;

interface ReflectionClassFactoryInterface
{
    public function create($class): \ReflectionClass;
}
