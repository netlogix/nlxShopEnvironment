<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Services;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Models\Config\Element;
use Shopware\Models\Config\Form;
use Shopware\Models\Shop\Template;
use Shopware\Models\Shop\TemplateConfig\Element as ThemeElement;
use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader implements ConfigurationLoaderInterface
{
    /** @var Container */
    private $container;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var array */
    private $errors;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    private function addError($errorMessage)
    {
        $this->errors[] = $errorMessage;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return false === empty($this->errors);
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration($pathToFile)
    {
        if (false === is_readable($pathToFile) && 'php://stdin' !== $pathToFile) {
            throw new \RuntimeException('file not found - ' . $pathToFile);
        }

        $this->entityManager = $this->container->get('models');

        $contentOfYamlFile = Yaml::parse(file_get_contents($pathToFile, false));
        if (isset($contentOfYamlFile['core_config'])) {
            $this->loadCoreConfiguration($contentOfYamlFile['core_config']);
        }

        if (isset($contentOfYamlFile['shop_config'])) {
            $this->loadShopConfiguration($contentOfYamlFile['shop_config']);
        }

        if (isset($contentOfYamlFile['theme_config'])) {
            $this->loadThemeConfiguration($contentOfYamlFile['theme_config']);
        }

        return false === $this->hasErrors();
    }

    /**
     * @param array $config
     */
    private function loadThemeConfiguration($config)
    {
        $configElementRepository = $this->entityManager->getRepository('Shopware\Models\Shop\TemplateConfig\Element');
        $configTemplateRepository = $this->entityManager->getRepository('Shopware\Models\Shop\Template');
        $configValueRepository = $this->entityManager->getRepository('Shopware\Models\Shop\TemplateConfig\Value');

        foreach ($config as $themeName => $themeValues) {
            $template = $configTemplateRepository->findOneBy(['name' => $themeName]);
            if (null === $template) {
                $this->addError(
                    '<comment>There is not Theme with the name "' . $themeName . '".</comment>'
                );
                continue;
            }

            foreach ($themeValues as $configName => $configValues) {
                $element = $this->findOrCreateThemeConfig($configElementRepository, $template, $configValues);
                if (null === $element) {
                    continue;
                }

                $element->setPosition($configValues['position']);
                $element->setSupportText($configValues['supportText']);

                $elementValues = $configValueRepository->findBy(['element' => $element]);

                foreach ($elementValues as $value) {
                    $value->setValue($configValues['value']);
                }
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param ObjectRepository $configElementRepository
     * @param Template         $template
     * @param array            $configValues
     *
     * @return ThemeElement|null|object
     */
    private function findOrCreateThemeConfig(ObjectRepository $configElementRepository, Template $template, $configValues)
    {
        $element = $configElementRepository->findOneBy(['name' => $configValues['name'], 'templateId' => $template->getId()]);
        if (null === $element) {
            $themeName = $template->getName();
            $elementName = $configValues['name'];
            $this->addError(
                '<comment>Theme "' . $themeName . '" has no configuration element for "' . $elementName . '". ' .
                'Configure it in the Theme.php</comment>'
            );
            return null;
        }

        return $element;
    }

    /**
     * @param array $config
     */
    private function loadCoreConfiguration($config)
    {
        $configElementRepository = $this->entityManager->getRepository('Shopware\Models\Config\Element');
        $configFormRepository = $this->entityManager->getRepository('Shopware\Models\Config\Form');

        foreach ($config as $nameOfBackendForm => $formElements) {
            foreach ($formElements as $elementName => $elementInformation) {
                $element = $this->findOrCreateElement($configElementRepository, $elementName, $elementInformation);
                $form = $this->findOrCreateForm($configFormRepository, $elementInformation);

                // update existing element
                $element->setForm($form);
                $element->setLabel($elementInformation['label']);
                $element->setDescription($elementInformation['description']);
                $element->setPosition($elementInformation['position']);
                $element->setScope($elementInformation['scope']);
                $element->setValue($elementInformation['value']);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param array $config
     */
    private function loadShopConfiguration($config)
    {
        $shopRepo = $this->entityManager->getRepository('Shopware\Models\Shop\Shop');

        foreach ($config as $id => $shopConfig) {
            $shop = $shopRepo->find($id);
            if (null === $shop) {
                echo
                    'The loadable configuration contains a shop with an ID that is not yet created in database. ' .
                    PHP_EOL .
                    'We cannot create new shops with custom IDs at the moment, so this shop cannot be configured now.' .
                    PHP_EOL .
                    'Problematic shop: ' . $id . PHP_EOL . PHP_EOL
                ;
                continue;
            }

            foreach ($shopConfig as $parameter => $value) {
                $reflectionClass = new \ReflectionClass('Shopware\Models\Shop\Shop');
                $setter = 'set' . $parameter;
                if (false === $reflectionClass->hasMethod($setter)) {
                    echo 'Property could not be imported as it does not exist: ' .
                        $parameter . ' (shop: ' . $id . ')' . PHP_EOL;
                }

                $shop->$setter($value);
            }

            $this->entityManager->persist($shop);
        }

        $this->entityManager->flush();
    }

    /**
     * @param ObjectRepository $configFormRepository
     * @param array            $elementInformation
     *
     * @return Form
     */
    private function findOrCreateForm(ObjectRepository $configFormRepository, $elementInformation)
    {
        /**
         * @todo at the moment a deleted or not existing forms are making problems (dublicate key), which I have to investigate
         * @todo for proper creation of form we would also need the plugin (there is a relation between forms and plugins)
         * @todo look at /vendor/shopware/shopware/engine/Shopware/Models/Config/Form.php:42
         */
        $form = $configFormRepository->findOneBy(['name' => $elementInformation['form']['name']]);
        if (null === $form) {
            $form = new Form();
            $this->entityManager->persist($form);
        }

        $form->setName($elementInformation['form']['name']);
        $form->setLabel($elementInformation['form']['label']);
        $form->setDescription($elementInformation['form']['description']);
        $form->setPosition($elementInformation['form']['position']);

        return $form;
    }

    /**
     * @param ObjectRepository $configElementRepository
     * @param string           $elementName
     * @param array            $elementInformation
     *
     * @return object|Element
     */
    private function findOrCreateElement(ObjectRepository $configElementRepository, $elementName, $elementInformation)
    {
        $element = $configElementRepository->findOneBy(['name' => $elementName, 'label' => $elementInformation['label']]);
        if (null === $element) {
            $elementType = $elementInformation['type'];
            $elementOptions = $elementInformation['options'];

            $element = new Element($elementType, $elementName, $elementOptions);
            $this->entityManager->persist($element);
        }

        return $element;
    }
}
