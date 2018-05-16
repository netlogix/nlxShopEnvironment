<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Services;

use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Config\Element;
use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader implements ConfigurationLoaderInterface
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration($pathToFile)
    {
        if (false === is_readable($pathToFile)) {
            throw new \RuntimeException('file not found - '.$pathToFile);
        }

        /** @var ModelManager $entityManager */
        $entityManager = $this->container->get('models');
        $configElementRepository = $entityManager->getRepository('Shopware\Models\Config\Element');

        $contentOfYamlFile = Yaml::parse($pathToFile);

        // @todo continue here when all data is dumped correctly
        /*foreach ($contentOfYamlFile as $nameOfBackendForm => $formElements) {
            foreach ($formElements as $elementName => $elementValue) {
                $element = $configElementRepository->findOneBy(['name' => $elementName]);
                if (null === $element) {
                    if (is_int($elementValue)) {

                    } elseif( is_array($elementValue)) {

                    }
                }
            }
        }*/


    }
}
