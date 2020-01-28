<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Commands;

use sdShopEnvironment\Services\ConfigurationDumperInterface;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ShopEnvironmentClearOpcacheCommand extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sd:environment:opcache:clear')
            ->setDescription('Clears the Opcache.')
            ->setHelp(
                'The <info>%command.name%</info> command will reset the Opcache.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (\function_exists('opcache_reset') && extension_loaded('Zend OPcache')) {
            $result = \opcache_reset();

            if ($result) {
                $output->writeln('<fg=green>Opcache successfully cleared.</>');
            } else {
                $output->writeln('<fg=yellow>Opcache is not enabled by the php configuration so nothing was done.</>');
            }
        } else {
            $output->writeln('<fg=yellow>Opcache extension is not installed so nothing was done.</>');
        }

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
