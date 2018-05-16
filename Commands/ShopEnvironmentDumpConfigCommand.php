<?php

namespace sdShopEnvironment\Commands;

use sdShopEnvironment\Services\ConfigurationDumperInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Commands\ShopwareCommand;

class ShopEnvironmentDumpConfigCommand extends ShopwareCommand
{
    /** @var ConfigurationDumperInterface */
    private $configurationDumper;

    private $exportPath = '';

    /**
     * @param ConfigurationDumperInterface $configurationDumper
     * @param string                       $defaultImportPath
     */
    public function __construct(
        ConfigurationDumperInterface $configurationDumper,
        $defaultImportPath
    ) {
        $this->configurationDumper = $configurationDumper;
        $this->exportPath = $defaultImportPath;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sd:environment:config:dump')
            ->addOption('filename', 'f', InputOption::VALUE_OPTIONAL, 'the name of the file where the configs should ' .
                'be exported to', 'shopware_configs.yaml')
            ->addOption('target-directory', 't', InputOption::VALUE_OPTIONAL, 'the location where the exported file ' .
                'should be placed', 'default')
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
        $targetDirectory = $input->getOption('target-directory');

        $this->configurationDumper->dumpConfiguration($this->exportPath . '/' . $filename);

        $output->writeln("<fg=yellow>Config values from `s_core_config_elements` were exported to " .
            "<fg=green>$this->exportPath/$filename</> succesfully</>");

        if ('default' !== $targetDirectory) {
            $output->writeln('moving file to '.$targetDirectory);
            if (false === is_writable($targetDirectory)) {
                $output->writeln("directory does not exist: <fg=red>$targetDirectory</>");
                exit(1);
            }
            if (rename($this->exportPath . '/' . $filename, $targetDirectory . '/' . $filename)) {
               $output->writeln('<fg=green>file successfully moved</>');
               exit(0);
            }
            $output->writeln('<fg=red>moving file to '.$targetDirectory.' failed!</>');
            exit(1);
        }
        exit(0);
    }
}
