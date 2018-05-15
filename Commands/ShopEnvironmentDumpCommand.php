<?php

namespace sdShopEnvironment\Commands;

use Shopware\Components\Model\ModelManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Commands\ShopwareCommand;

class ShopEnvironmentDumpCommand extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sd:environment:dump')
            ->setDescription('Dumps the current configs from the database to ')
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
        $output->writeln('I`m executed');
    }
}
