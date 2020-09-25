<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Services;

interface ConfigurationDumperInterface
{
    /**
     * @param string $exportPath
     */
    public function dumpConfiguration($exportPath);
}
