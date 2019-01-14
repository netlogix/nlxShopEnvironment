<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Services;

use sdShopEnvironment\DataTypes\DataTypeCollectorInterface;
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

    public function dumpConfiguration($exportPath = 'php://stdout')
    {
        $configuration = [];

        $types = $this->dataTypeCollector->getAll();
        foreach ($types as $rootName => $type) {
            $configuration[$rootName] = $type->getDumper()->dump();
        }

        $configurationAsYaml = Yaml::dump($configuration, 4, 4, true, false);

        if (false === \is_writable(dirname($exportPath)) && 'php://stdout' !== $exportPath) {
            \mkdir(\dirname($exportPath), 0775);
        }

        \file_put_contents($exportPath, $configurationAsYaml);
    }
}
