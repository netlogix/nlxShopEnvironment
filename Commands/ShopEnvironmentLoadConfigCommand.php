<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Commands;

use nlxShopEnvironment\Services\ConfigurationLoaderInterface;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShopEnvironmentLoadConfigCommand extends ShopwareCommand
{
    /** @var ConfigurationLoaderInterface */
    private $configurationLoader;

    public function __construct(
        ConfigurationLoaderInterface $configurationLoader
    ) {
        $this->configurationLoader = $configurationLoader;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sd:environment:config:load')
            ->addOption(
                'file',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Name and path of the file that should be used to import values from, use - for stdin.',
                'shopware_config.yaml'
            )
            ->setDescription('Loads the current config from a YAML file into the database.')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> will load all config-values from the given file into the `s_core_config_elements` table.
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $errorOutput = $this->getErrorOutput($output);

        $filename = \trim($input->getOption('file'));
        if ('-' === $filename) {
            $filename = 'php://stdin';
        } elseif (false === \file_exists($filename)) {
            $errorOutput->writeln('<error>File not found: ' . $filename . '</error>');
            exit(1);
        }

        $this->configurationLoader->loadConfiguration($filename);

        $errorOutput->writeln('<info>Imported file: ' . $filename . '</info>');
    }
    
    private function getErrorOutput(OutputInterface $output): OutputInterface
    {
        if ($output instanceof ConsoleOutputInterface) {
            return $output->getErrorOutput();
        }

        return $output;
    }
}
