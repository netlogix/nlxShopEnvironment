<?php

namespace sdShopEnvironment\Commands;

use sdShopEnvironment\Services\ConfigurationDumperInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Commands\ShopwareCommand;

class ShopEnvironmentDumpCommand extends ShopwareCommand
{
    /** @var ConfigurationDumperInterface */
    private $configurationDumper;

    private $exportPath = '';

    /**
     * ShopEnvironmentDumpCommand constructor.
     * @param ConfigurationDumperInterface $configurationDumper
     * @param string                       $defaultExportPath
     */
    public function __construct(
        ConfigurationDumperInterface $configurationDumper,
        $defaultExportPath
    ) {
        $this->configurationDumper = $configurationDumper;
        $this->exportPath = $defaultExportPath;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sd:environment:dump')
            ->addOption('filename', 'f', InputOption::VALUE_OPTIONAL, 'the name of the file where the configs should be exported to', 'shopware_configs.yaml')
            ->setDescription('Dumps the current configs from the database to a yaml-File')
            ->setHelp(<<<EOF
The <info>%command.name%</info> will dump all relevant config-values to a file.
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getOption('filename');
        $this->configurationDumper->dumpConfiguration($this->exportPath . '/' . $filename);
    }
}
