<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Services;

interface ConfigurationLoaderInterface
{
    /**
     * @param string $pathToFile
     *
     * @return bool
     */
    public function loadConfiguration($pathToFile);
}
