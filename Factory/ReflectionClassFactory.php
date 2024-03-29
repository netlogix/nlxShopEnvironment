<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Factory;

class ReflectionClassFactory implements ReflectionClassFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create($class): \ReflectionClass
    {
        return new \ReflectionClass($class);
    }
}
