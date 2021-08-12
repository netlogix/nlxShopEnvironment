<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Factory;

interface ReflectionClassFactoryInterface
{
    /**
     * @param mixed $class
     */
    public function create($class): \ReflectionClass;
}
