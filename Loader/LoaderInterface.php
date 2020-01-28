<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sdShopEnvironment\Loader;

interface LoaderInterface
{
    /**
     * @param array|mixed[] $config
     */
    public function load($config);
}
