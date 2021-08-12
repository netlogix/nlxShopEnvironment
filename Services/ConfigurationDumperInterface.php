<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Services;

interface ConfigurationDumperInterface
{
    public function dumpConfiguration(string $exportPath): void;
}
