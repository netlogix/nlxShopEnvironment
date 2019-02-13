<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

interface LoaderInterface
{
    /**
     * @param array|mixed[] $config
     */
    public function load($config);
}
