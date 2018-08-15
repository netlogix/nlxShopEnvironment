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
use Shopware\Models\Shop\Template;
use Shopware\Models\Shop\TemplateConfig\Element as ThemeElement;
use Shopware\Models\Shop\TemplateConfig\Value as ThemeElementValue;
use Symfony\Component\Yaml\Yaml;

class ConfigurationDumper implements ConfigurationDumperInterface
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function dumpConfiguration($exportPath = 'php://stdout')
    {
        $configuration = [];

        $configuration['core_config'] = $this->getCoreConfig();
        $configuration['shop_config'] = $this->getShopConfig();
        $configuration['theme_config'] = $this->getThemeConfig();

        $configurationAsYaml = Yaml::dump($configuration, 4, 4, true, false);

        if (false === is_writable(dirname($exportPath)) && 'php://stdout' !== $exportPath) {
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
    private function getThemeConfig()
    {
        /** @var ModelManager $entityManager */
        $entityManager = $this->container->get('models');
        $configElementRepository = $entityManager->getRepository('Shopware\Models\Shop\TemplateConfig\Element');

        $allConfigs = $configElementRepository->findAll();

        $configuration = [];

        foreach ($allConfigs as $element) {
            /* @var $element ThemeElement */
            $configValues = $element->getValues()->toArray();
            $template = $element->getTemplate();

            $this->addThemeElementValues($element, $template, $configValues, $configuration);
            $this->addThemeElementInformation($element, $template, $configuration);
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
                'Name'              => $shop->getName(),
                'Title'             => $shop->getTitle(),
                'Host'              => $shop->getHost(),
                'BasePath'          => $shop->getBasePath(),
                'BaseUrl'           => $shop->getBaseUrl(),
                'Hosts'             => $shop->getHosts(),
                'Secure'            => $shop->getSecure(),
                'CustomerScope'     => $shop->getCustomerScope(),
                'Default'           => $shop->getDefault(),
                'Active'            => $shop->getActive(),
            ];
        }

        return $shopConfigs;
    }

    /**
     * @param ThemeElement $element
     * @param Template     $template
     * @param array        $configValues
     * @param array        $configuration
     */
    private function addThemeElementValues(ThemeElement $element, Template $template, $configValues, &$configuration)
    {
        foreach ($configValues as $value) {
            if ($value instanceof ThemeElementValue) {
                $value = $value->getValue();
            }

            $configuration[$template->getName()][$element->getName()]['value'] = $value;
        }
    }

    /**
     * @param Element $element
     * @param Form    $backendForm
     * @param array   $configValue
     * @param array   $configuration
     */
    private function addElementWithMultipleValues(Element $element, Form $backendForm, $configValue, &$configuration)
    {
        /** @var \Shopware_Components_Config $config */
        $config = $this->container->get('Config');
        $configValues = $config->get($element->getName());

        foreach ($configValues as $value) {
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
        /** @var \Shopware_Components_Config $config */
        $config = $this->container->get('Config');
        $value = $config->get($element->getName());

        $configuration[$backendForm->getName()][$element->getName()]['value'] = $value;
    }

    /**
     * @param ThemeElement $element
     * @param Template     $template
     * @param array        $configuration
     */
    private function addThemeElementInformation(ThemeElement $element, Template $template, &$configuration)
    {
        $templateName = $template->getName();
        $elementName = $element->getName();

        $configuration[$templateName][$elementName]['name']         = $elementName;
        $configuration[$templateName][$elementName]['type']         = $element->getType();
        $configuration[$templateName][$elementName]['position']     = $element->getPosition();
        $configuration[$templateName][$elementName]['defaultValue'] = $element->getDefaultValue();
        $configuration[$templateName][$elementName]['fieldLabel']   = $element->getFieldLabel();
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
