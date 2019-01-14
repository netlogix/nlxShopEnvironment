<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

interface LoaderInterface
{
    /**
     * @param array|mixed[] $config
     */
    public function load($config);
}
