<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sdShopEnvironment\Commands;

use sdShopEnvironment\Services\CacheCleaners\CacheCleanerInterface;
use Shopware\Commands\ShopwareCommand;
use Shopware\Components\HttpClient\HttpClientInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShopEnvironmentClearOpcacheCommand extends ShopwareCommand
{
    /** @var HttpClientInterface */
    private $httpClient;

    /** @var CacheCleanerInterface */
    private $opcacheCleaner;

    public function __construct(HttpClientInterface $httpClient, CacheCleanerInterface $opcacheCleaner)
    {
        $this->httpClient = $httpClient;
        $this->opcacheCleaner = $opcacheCleaner;
        parent::__construct();
    }

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
        if ($this->cleanCliOpcache()) {
            $output->writeln('<fg=green>Cli Opcache successfully cleared.</>');
        } else {
            $output->writeln('<fg=yellow>Cli Opcache could not be cleared. Maybe Opcache is disabled for Cli?.</>');
        }

        if ($this->cleanWebServerOpcache()) {
            $output->writeln('<fg=green>Web server Opcache successfully cleared.</>');
        } else {
            $output->writeln('<fg=red>A server error occurred while cleaning the web server opcache.</>');
            exit(255);
        }

        exit(0);
    }

    private function cleanCliOpcache(): bool
    {
        return $this->opcacheCleaner->clean();
    }

    private function cleanWebServerOpcache(): bool
    {
        $shopUrl = \getenv('SHOP_URL');
        $shopHost = \parse_url($shopUrl, \PHP_URL_HOST);

        $response = $this->httpClient->get('https://127.0.0.1/CleanOpcache', [
            'host' => $shopHost,
        ]);
        $statusCode = $response->getStatusCode();
        return '200' === $statusCode;
    }
}
