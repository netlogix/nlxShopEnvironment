<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Services;

interface ConfigurationDumperInterface
{
    /**
     * @param string $exportPath
     */
    public function dumpConfiguration($exportPath);
}
