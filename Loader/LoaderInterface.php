<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Loader;

interface LoaderInterface
{
    /**
     * @param array|mixed[] $config
     */
    public function load($config);
}
