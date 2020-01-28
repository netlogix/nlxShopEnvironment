<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sdShopEnvironment\Commands;

use sdShopEnvironment\Services\ConfigurationDumperInterface;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ShopEnvironmentDumpConfigCommand extends ShopwareCommand
{
    /** @var ConfigurationDumperInterface */
    private $configurationDumper;

    /**
     * @param ConfigurationDumperInterface $configurationDumper
     */
    public function __construct(
        ConfigurationDumperInterface $configurationDumper
    ) {
        $this->configurationDumper = $configurationDumper;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sd:environment:config:dump')
            ->addOption(
                'file',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Name and path of the file where the configs should be exported to, use - for stdout.',
                'shopware_config.yaml'
            )
            ->setDescription('Dumps the current config from the database to a YAML file.')
            ->setHelp(
                'The <info>%command.name%</info> will dump all relevant config-values to a file.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = \trim($input->getOption('file'));
        if ('-' === $filename) {
            $filename = 'php://stdout';
        }

        $this->configurationDumper->dumpConfiguration($filename);

        $errorOutput = $this->getErrorOutput($output, $filename);
        $errorOutput->writeln(
            '<fg=yellow>Config values from `s_core_config_elements` have successfully been exported to ' .
            "<fg=green>$filename</></>."
        );

        exit(0);
    }

    /**
     * @param OutputInterface $output
     * @param string          $filename
     *
     * @return OutputInterface
     */
    private function getErrorOutput(OutputInterface $output, $filename = '')
    {
        if ($output instanceof ConsoleOutputInterface) {
            return $output->getErrorOutput();
        } elseif ('php://stdout' !== $filename) {
            return $output;
        }

        return new NullOutput();
    }
}
