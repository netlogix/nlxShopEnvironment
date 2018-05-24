<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Services;

interface ConfigurationLoaderInterface
{
    /**
     * @param string $pathToFile
     */
    public function loadConfiguration($pathToFile);
}
