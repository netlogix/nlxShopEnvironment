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

    /** @var array */
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

                if (is_array($configValue)) {
                    $this->addElementWithMultipleValues($element, $backendForm, $configValue);
                } else {
                    $this->addElementWithSingleValue($element, $backendForm);
                }

                $this->addElementInformation($element, $backendForm);
                $this->addFormInformation($element, $backendForm);

            } catch (EntityNotFoundException $entityNotFoundException) {
                // @todo think of what to do here. The try-catch is necessary since there seems to be the
                // @todo possibility, that there are values assigned to forms that do not exist. (id = 0)
            }
        }

        $configurationAsYaml = Yaml::dump($this->configurationAsArray, 4);

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
    private function addElementWithMultipleValues(Element $element, Form $backendForm, $configValue)
    {
        foreach ($configValue as $value) {
            $this->configurationAsArray[$backendForm->getName()][$element->getName()]['value'][] = $value;
        }
    }

    private function addElementWithSingleValue(Element $element, Form $backendForm)
    {
        $this->configurationAsArray[$backendForm->getName()][$element->getName()]['value'] = $element->getValue();
    }

    private function addElementInformation(Element $element, Form $backendForm)
    {
        $formName = $backendForm->getName();
        $elementName = $element->getName();

        $this->configurationAsArray[$formName][$elementName]['name']        = $elementName;
        $this->configurationAsArray[$formName][$elementName]['label']       = $element->getLabel();
        $this->configurationAsArray[$formName][$elementName]['description'] = $element->getDescription();
        $this->configurationAsArray[$formName][$elementName]['type']        = $element->getType();
        $this->configurationAsArray[$formName][$elementName]['required']    = $element->getRequired();
        $this->configurationAsArray[$formName][$elementName]['position']    = $element->getPosition();
        $this->configurationAsArray[$formName][$elementName]['scope']       = $element->getScope();
        $this->configurationAsArray[$formName][$elementName]['options']     = $element->getOptions();
    }

    private function addFormInformation(Element $element, Form $backendForm)
    {
        $this->configurationAsArray[$backendForm->getName()][$element->getName()]['form'] =
            [
                'id'          => $backendForm->getId(),
                'parent_id'   => is_null($backendForm->getParent()) ? null: $backendForm->getParent()->getId(),
                'name'        => $backendForm->getName(),
                'label'       => $backendForm->getLabel(),
                'description' => $backendForm->getDescription(),
                'position'    => $backendForm->getPosition(),
                'plugin_id'   => $backendForm->getPluginId(),
            ]
        ;
    }
}
