<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Services;

interface ConfigurationLoaderInterface
{
    /**
     * @param string $pathToFile
     *
     * @return bool
     */
    public function loadConfiguration($pathToFile);
}
