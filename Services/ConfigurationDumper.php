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
use Shopware\Models\Shop\Shop;
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
        $configuration = [];

        $configuration['core_config'] = $this->getCoreConfig();
        $configuration['shop_config'] = $this->getShopConfig();

        $configurationAsYaml = Yaml::dump($configuration, 4);

        if (false === is_writable(dirname($exportPath))) {
            mkdir(dirname($exportPath), 0775);
        }

        file_put_contents($exportPath, $configurationAsYaml);
    }

    /**
     * @return array
     */
    private function getCoreConfig()
    {
        /** @var ModelManager $entityManager */
        $entityManager = $this->container->get('models');
        $configElementRepository = $entityManager->getRepository('Shopware\Models\Config\Element');

        $allConfigs = $configElementRepository->findAll();

        $configuration = [];

        foreach ($allConfigs as $element) {
            /* @var $element Element */
            try {
                $configValue = $element->getValue();
                $backendForm = $element->getForm();

                if (is_array($configValue)) {
                    $this->addElementWithMultipleValues($element, $backendForm, $configValue, $configuration);
                } else {
                    $this->addElementWithSingleValue($element, $backendForm, $configuration);
                }

                $this->addElementInformation($element, $backendForm, $configuration);
                $this->addFormInformation($element, $backendForm, $configuration);
            } catch (EntityNotFoundException $entityNotFoundException) {
                // @todo think of what to do here. The try-catch is necessary since there seems to be the
                // @todo possibility, that there are values assigned to forms that do not exist. (id = 0)
            }
        }

        return $configuration;
    }

    /**
     * @return array
     */
    private function getShopConfig()
    {
        $shopConfigs = [];

        /** @var ModelManager $entityManager */
        $entityManager = $this->container->get('models');
        $shopRepo = $entityManager->getRepository('Shopware\Models\Shop\Shop');

        $shops = $shopRepo->findAll();
        /** @var Shop $shop */
        foreach ($shops as $shop) {
            $shopConfigs[$shop->getId()] = [
                'name'              => $shop->getName(),
                'title'             => $shop->getTitle(),
                'host'              => $shop->getHost(),
                'base_path'         => $shop->getBasePath(),
                'base_url'          => $shop->getBaseUrl(),
                'hosts'             => $shop->getHosts(),
                'secure'            => $shop->getSecure(),
                'customer_scope'    => $shop->getCustomerScope(),
                'default'           => $shop->getDefault(),
                'active'            => $shop->getActive(),
            ];
        }

        return $shopConfigs;
    }

    /**
     * @param Element $element
     * @param Form    $backendForm
     * @param array   $configValue
     * @param array   $configuration
     */
    private function addElementWithMultipleValues(Element $element, Form $backendForm, $configValue, &$configuration)
    {
        foreach ($configValue as $value) {
            $configuration[$backendForm->getName()][$element->getName()]['value'][] = $value;
        }
    }

    /**
     * @param Element $element
     * @param Form    $backendForm
     * @param array   $configuration
     */
    private function addElementWithSingleValue(Element $element, Form $backendForm, &$configuration)
    {
        $configuration[$backendForm->getName()][$element->getName()]['value'] = $element->getValue();
    }

    /**
     * @param Element $element
     * @param Form    $backendForm
     * @param array   $configuration
     */
    private function addElementInformation(Element $element, Form $backendForm, &$configuration)
    {
        $formName = $backendForm->getName();
        $elementName = $element->getName();

        $configuration[$formName][$elementName]['name']        = $elementName;
        $configuration[$formName][$elementName]['label']       = $element->getLabel();
        $configuration[$formName][$elementName]['description'] = $element->getDescription();
        $configuration[$formName][$elementName]['type']        = $element->getType();
        $configuration[$formName][$elementName]['required']    = $element->getRequired();
        $configuration[$formName][$elementName]['position']    = $element->getPosition();
        $configuration[$formName][$elementName]['scope']       = $element->getScope();
        $configuration[$formName][$elementName]['options']     = $element->getOptions();
    }

    /**
     * @param Element $element
     * @param Form    $backendForm
     * @param array   $configuration
     */
    private function addFormInformation(Element $element, Form $backendForm, &$configuration)
    {
        $configuration[$backendForm->getName()][$element->getName()]['form'] =
            [
                'name'        => $backendForm->getName(),
                'label'       => $backendForm->getLabel(),
                'description' => $backendForm->getDescription(),
                'position'    => $backendForm->getPosition(),
                'plugin_id'   => $backendForm->getPluginId(),
            ]
        ;
    }
}
