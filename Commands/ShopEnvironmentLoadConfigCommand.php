<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Commands;

use sdShopEnvironment\Services\ConfigurationLoaderInterface;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
            ->setDescription('Loads the current configs from a yaml-File to the database')
            ->setHelp(<<<EOF
The <info>%command.name%</info> will load all config-values from the given file into the `s_core_config_elements` table.
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getOption('file');

        $fileLocation = $this->importPath . '/' . $filename;

        if (false === file_exists($fileLocation)) {
            $output->writeln('File not found - ' . $fileLocation);
            exit(1);
        }

        $this->configurationLoader->loadConfiguration($fileLocation);
    }
}
