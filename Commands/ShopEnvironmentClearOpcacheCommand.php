<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sdShopEnvironment\Commands;

use Shopware\Commands\ShopwareCommand;
use Shopware\Components\HttpClient\HttpClientInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShopEnvironmentClearOpcacheCommand extends ShopwareCommand
{
    /** @var HttpClientInterface */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
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
        $shopUrl = \getenv('SHOP_URL');
        $shopHost = \parse_url($shopUrl, \PHP_URL_HOST);

        $response = $this->httpClient->get('https://127.0.0.1/ClearOpcache', [
            'host' => $shopHost,
        ]);
        $statusCode = $response->getStatusCode();
        if ('200' === $statusCode) {
            $output->writeln('<fg=green>Opcache successfully cleared.</>');
        } else {
            $output->writeln('<fg=red>A server error occurred while cleaning the opcache.</>');
            exit(255);
        }

        exit(0);
    }
}
