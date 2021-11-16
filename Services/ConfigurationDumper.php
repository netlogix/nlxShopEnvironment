<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Services;

use nlxShopEnvironment\DataTypes\DataTypeCollectorInterface;
use Symfony\Component\Yaml\Yaml;

class ConfigurationDumper implements ConfigurationDumperInterface
{
    /** @var DataTypeCollectorInterface */
    private $dataTypeCollector;

    public function __construct(
        DataTypeCollectorInterface $dataTypeCollector
    ) {
        $this->dataTypeCollector = $dataTypeCollector;
    }

    public function dumpConfiguration(string $exportPath = 'php://stdout'): void
    {
        $configuration = [];

        $types = $this->dataTypeCollector->getAll();
        foreach ($types as $rootName => $type) {
            $configuration[$rootName] = $type->getDumper()->dump();
        }

        $configurationAsYaml = Yaml::dump($configuration, 4, 4, Yaml::DUMP_OBJECT);

        if (false === \is_writable(\dirname($exportPath)) && 'php://stdout' !== $exportPath) {
            \mkdir(\dirname($exportPath), 0775);
        }

        \file_put_contents($exportPath, $configurationAsYaml);
    }
}
