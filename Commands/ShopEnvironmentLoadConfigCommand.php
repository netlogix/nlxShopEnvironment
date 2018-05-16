<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Commands;

use sdShopEnvironment\Services\ConfigurationLoaderInterface;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputOption;

class ShopEnvironmentLoadConfigCommand extends ShopwareCommand
{
    /** @var ConfigurationLoaderInterface */
    private $configurationLoader;

    /** @var string */
    private $importPath;

    /**
     * @param ConfigurationLoaderInterface $configurationLoader
     * @param string                       $defaultImportPath
     */
    public function __construct(
        ConfigurationLoaderInterface $configurationLoader,
        $defaultImportPath
    ) {
        $this->configurationLoader = $configurationLoader;
        $this->importPath = $defaultImportPath;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sd:environment:config:load')
            ->addOption('file', 'f', InputOption::VALUE_OPTIONAL, 'the path to the file which should be used ' .
                'for the import', 'shopware_configs.yaml')
            ->setDescription('Dumps the current configs from the database to a yaml-File')
            ->setHelp(<<<EOF
The <info>%command.name%</info> will load all config-values from the given file into the `s_core_config_elements` table.
EOF
            );
    }
}
