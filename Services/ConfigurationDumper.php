<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use sdShopEnvironment\DataTypes\DataTypeCollectorInterface;
use Shopware\Components\ConfigWriter;
use Shopware\Models\Config\Element;
use Shopware\Models\Config\Form;
use Shopware\Models\Shop\Shop;
use Shopware\Models\Shop\Template;
use Shopware\Models\Shop\TemplateConfig\Element as ThemeElement;
use Shopware\Models\Shop\TemplateConfig\Value as ThemeElementValue;
use Symfony\Component\Yaml\Yaml;

class ConfigurationDumper implements ConfigurationDumperInterface
{
    const NO_FORM_NAME = '__NO_FORM__';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ConfigWriter */
    private $configWriter;

    /** @var array|Shop[] */
    private $shops;

    /** @var DataTypeCollectorInterface */
    private $dataTypeCollector;

    public function __construct(
        EntityManagerInterface $entityManager,
        ConfigWriter $configWriter,
        DataTypeCollectorInterface $dataTypeCollector
    ) {
        $this->entityManager = $entityManager;
        $this->configWriter = $configWriter;
        $this->dataTypeCollector = $dataTypeCollector;
    }

    public function dumpConfiguration($exportPath = 'php://stdout')
    {
        $configuration = [];

        $types = $this->dataTypeCollector->getAll();
        foreach ($types as $rootName => $type) {
            $configuration[$rootName] = $type->getDumper()->dump();
        }

        $configurationAsYaml = Yaml::dump($configuration, 4, 4, true, false);

        if (false === \is_writable(dirname($exportPath)) && 'php://stdout' !== $exportPath) {
            \mkdir(\dirname($exportPath), 0775);
        }

        \file_put_contents($exportPath, $configurationAsYaml);
    }

    /**
     * @return array
     */
    private function getCoreConfig()
    {
        $configElementRepository = $this->entityManager->getRepository('Shopware\Models\Config\Element');

        $allConfigs = $configElementRepository->findAll();

        $configuration = [];

        foreach ($allConfigs as $element) {
            /* @var $element Element */
            $configValue = $element->getValue();
            try {
                // Try to load form and get an arbitrary property (lazy loading!) to check if form exists
                $backendForm = $element->getForm();
                $backendForm->getName();
            } catch (EntityNotFoundException $entityNotFoundException) {
                $backendForm = null;
            }

            $this->addElement($element, $backendForm, $configValue, $configuration);
            $this->addElementInformation($element, $backendForm, $configuration);

            if (null !== $backendForm) {
                $this->addFormInformation($element, $backendForm, $configuration);
            }
        }

        return $configuration;
    }

    /**
     * @return array
     */
    private function getThemeConfig()
    {
        $configElementRepository = $this->entityManager->getRepository('Shopware\Models\Shop\TemplateConfig\Element');

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

        $shopRepo = $this->entityManager->getRepository('Shopware\Models\Shop\Shop');

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
     * @return array|Shop[]
     */
    private function getShops()
    {
        if (null === $this->shops) {
            /** @var $repository \Shopware\Models\Shop\Repository */
            $repository = $this->entityManager->getRepository(Shop::class);
            $this->shops = $repository->findAll();
        }

        return $this->shops;
    }

    /**
     * @param null|Form $backendForm
     *
     * @return string
     */
    private function getFormName($backendForm)
    {
        if (null === $backendForm) {
            return self::NO_FORM_NAME;
        }

        return $backendForm->getName();
    }

    /**
     * @param Element   $element
     * @param null|Form $backendForm
     * @param mixed     $defaultValue
     * @param array     $configuration
     */
    private function addElement(Element $element, $backendForm, $defaultValue, &$configuration)
    {
        $formName = $this->getFormName($backendForm);

        $shops = $this->getShops();

        $values = [];
        foreach ($shops as $shop) {
            $value = $this->configWriter->get($element->getName(), $formName, $shop->getId());
            if ($value !== $defaultValue) {
                $values[$shop->getId()] = $value;
            }
        }

        $configuration[$formName][$element->getName()]['defaultValue'] = $defaultValue;
        $configuration[$formName][$element->getName()]['shopValues'] = $values;
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
     * @param Element   $element
     * @param null|Form $backendForm
     * @param array     $configuration
     */
    private function addElementInformation(Element $element, $backendForm, &$configuration)
    {
        $formName = $this->getFormName($backendForm);
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
