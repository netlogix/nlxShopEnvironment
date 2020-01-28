<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sdShopEnvironment\Services;

interface ConfigurationDumperInterface
{
    /**
     * @param string $exportPath
     */
    public function dumpConfiguration($exportPath);
}
