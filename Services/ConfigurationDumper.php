<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Services;

use Doctrine\ORM\EntityNotFoundException;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Config\Element;
use Symfony\Component\Yaml\Yaml;

class ConfigurationDumper implements ConfigurationDumperInterface
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function dumpConfiguration($exportPath = '')
    {
        /** @var ModelManager $entityManager */
        
        $entityManager = $this->container->get('models');
        $configElementRepository = $entityManager->getRepository('Shopware\Models\Config\Element');

        $allConfigs = $configElementRepository->findAll();

        $configurationAsArray = [];

        foreach($allConfigs as $element) {
            try {

                $configValue = $element->getValue();
                $backendForm = $element->getForm();

                $backendCategory = $backendForm->getName();

                if (is_array($configValue)) {
                    foreach ($configValue as $value) {
                        $configurationAsArray[$backendCategory][] = $value;
                    }
                } else {
                    /** @var $element Element */
                    $configurationAsArray[$backendCategory][$element->getName()] = $element->getValue();
                }
            } catch (EntityNotFoundException $entityNotFoundException) {
                // @todo think of what to do here. The try-catch is necessary since there seems to be the
                // @todo possibility, that there are values assigned to forms that do not exist. (id = 0)
            }
        }

        $configurationAsYaml = Yaml::dump($configurationAsArray);

        if (false === is_writable(dirname($exportPath))) {
            mkdir(dirname($exportPath), 0775);
        }

        file_put_contents($exportPath, $configurationAsYaml);
    }
}
