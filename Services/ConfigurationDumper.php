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
use Shopware\Models\Config\Form;
use Symfony\Component\Yaml\Yaml;

class ConfigurationDumper implements ConfigurationDumperInterface
{
    /** @var Container */
    private $container;

    private $configurationAsArray;

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

        $this->configurationAsArray = [];

        foreach($allConfigs as $element) {
            /** @var $element Element */
            try {
                $configValue = $element->getValue();
                $backendForm = $element->getForm();

                $backendCategory = $backendForm->getName();

                if (is_array($configValue)) {
                    $this->addMultipleValues($element, $backendForm, $configValue);
                } else {
                    $this->addSingleValue($element, $backendForm);
                }
            } catch (EntityNotFoundException $entityNotFoundException) {
                // @todo think of what to do here. The try-catch is necessary since there seems to be the
                // @todo possibility, that there are values assigned to forms that do not exist. (id = 0)
            }
        }

        $configurationAsYaml = Yaml::dump($this->configurationAsArray);

        if (false === is_writable(dirname($exportPath))) {
            mkdir(dirname($exportPath), 0775);
        }

        file_put_contents($exportPath, $configurationAsYaml);
    }

    /**
     * @param Element $element
     * @param Form $backendForm
     * @param array $configValue
     */
    private function addMultipleValues(Element $element, Form $backendForm, $configValue)
    {
        foreach ($configValue as $value) {
            $this->configurationAsArray[$backendForm->getName()][$element->getName()]['value'][] = $value;
        }
        $this->addElementName($element, $backendForm);
    }

    private function addSingleValue(Element $element, Form $backendForm)
    {
        $this->configurationAsArray[$backendForm->getName()][$element->getName()]['value'] = $element->getValue();
        $this->addElementName($element, $backendForm);
    }

    private function addElementName(Element $element, Form $backendForm)
    {
        $this->configurationAsArray[$backendForm->getName()][$element->getName()]['name'] = $element->getName();
    }
}
