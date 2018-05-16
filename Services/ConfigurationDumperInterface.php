<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Services;

interface ConfigurationDumperInterface
{
    /**
     * @param string $exportPath
     */
    public function dumpConfiguration($exportPath);
}
