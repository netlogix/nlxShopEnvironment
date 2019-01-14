<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Services;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;
use sdShopEnvironment\DataTypes\DataTypeCollectorInterface;
use Shopware\Components\ConfigWriter;
use Shopware\Models\Config\Element;
use Shopware\Models\Config\Form;
use Shopware\Models\Shop\Shop;
use Shopware\Models\Shop\Template;
use Shopware\Models\Shop\TemplateConfig\Element as ThemeElement;
use Shopware\Models\Shop\TemplateConfig\Value;
use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader implements ConfigurationLoaderInterface
{
    use LoggingTrait;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ConfigWriter */
    private $configWriter;

    /** @var Connection */
    private $connection;

    /** @var DataTypeCollectorInterface */
    private $dataTypeCollector;

    public function __construct(
        EntityManagerInterface $entityManager,
        ConfigWriter $configWriter,
        Connection $connection,
        DataTypeCollectorInterface $dataTypeCollector
    ) {
        $this->entityManager = $entityManager;
        $this->configWriter = $configWriter;
        $this->connection = $connection;
        $this->dataTypeCollector = $dataTypeCollector;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration($pathToFile)
    {
        if (false === is_readable($pathToFile) && 'php://stdin' !== $pathToFile) {
            throw new \RuntimeException('file not found - ' . $pathToFile);
        }

        $contentOfYamlFile = Yaml::parse(file_get_contents($pathToFile, false));
        foreach ($contentOfYamlFile as $rootName => $content) {
            $type = $this->dataTypeCollector->get($rootName);
            if (null === $type) {
                $this->addError('Configuration file contains unknown root entry `' . $rootName . '`. Aborting.');
                continue;
            }

            $loader = $type->getLoader();
            if (null === $loader) {
                $this->addError('Data of type `' . $rootName . '` cannot be loaded. Aborting.');
                continue;
            }

            $loader->load($content);
        }

        return false === $this->hasErrors();
    }

    /**
     * @param array $config
     */
    private function loadThemeConfiguration($config)
    {
        $configElementRepository = $this->entityManager->getRepository(ThemeElement::class);
        $configTemplateRepository = $this->entityManager->getRepository(Template::class);
        $configValueRepository = $this->entityManager->getRepository(Value::class);
        $shopRepository = $this->entityManager->getRepository(Shop::class);

        foreach ($config as $themeName => $themeValues) {
            $template = $configTemplateRepository->findOneBy(['name' => $themeName]);
            if (null === $template) {
                $this->addWarning(
                    'There is no theme with name "' . $themeName . '".'
                );
                continue;
            }

            foreach ($themeValues as $configName => $configValues) {
                $element = $this->findOrCreateThemeConfig($configElementRepository, $template, $configValues);
                if (null === $element) {
                    continue;
                }

                $element->setPosition(isset($configValues['position']) ? $configValues['position'] : 0);
                $element->setSupportText(isset($configValues['supportText']) ? $configValues['supportText'] : '');
                $element->setDefaultValue(isset($configValues['defaultValue']) ? $configValues['defaultValue'] : '');

                if (isset($configValues['shopValues'])) {
                    foreach ($configValues['shopValues'] as $shopId => $configValue) {
                        /** @var Shop $shop */
                        $shop = $shopRepository->find($shopId);
                        $this->findOrCreateThemeConfigValue($configValueRepository, $element, $shop, $configValue);
                    }
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
            $this->addWarning(
                'Theme "' . $themeName . '" has no configuration element for "' . $elementName . '". ' .
                'Configure it in the Theme.php'
            );
            return null;
        }

        return $element;
    }

    /**
     * @param ObjectRepository $configValueRepository
     * @param ThemeElement     $element
     * @param Shop             $shop
     * @param mixed            $configValue
     *
     * @return Value
     */
    private function findOrCreateThemeConfigValue(
        ObjectRepository $configValueRepository,
        ThemeElement $element,
        Shop $shop,
        $configValue
    ) {
        $elementValue = $configValueRepository->findOneBy(['element' => $element, 'shop' => $shop]);
        if (null === $elementValue) {
            $elementValue = new Value();
            $elementValue->setElement($element);
            $elementValue->setShop($shop);
            $elementValue->setValue($configValue);
            $this->entityManager->persist($elementValue);
        } else {
            $elementValue->setValue($configValue);
        }

        return $elementValue;
    }

    /**
     * @param array $config
     */
    private function loadCoreConfiguration($config)
    {
        $configElementRepository = $this->entityManager->getRepository('Shopware\Models\Config\Element');
        $configFormRepository = $this->entityManager->getRepository('Shopware\Models\Config\Form');

        // First load config elements and forms (structure and defaults)
        foreach ($config as $nameOfBackendForm => $formElements) {
            foreach ($formElements as $elementName => $elementInformation) {
                // Load form if it is set
                if (ConfigurationDumper::NO_FORM_NAME !== $nameOfBackendForm) {
                    $form = $this->findOrCreateForm($configFormRepository, $nameOfBackendForm, $elementInformation);
                } else {
                    $form = null;
                }

                $element =
                    $this->findOrCreateElement($configElementRepository, $form, $elementName, $elementInformation);

                if (null !== $form) {
                    $element->setForm($form);
                }

                if (array_key_exists('label', $elementInformation)) {
                    $element->setLabel($elementInformation['label']);
                }

                if (array_key_exists('description', $elementInformation)) {
                    $element->setDescription($elementInformation['description']);
                }

                if (array_key_exists('position', $elementInformation)) {
                    $element->setPosition($elementInformation['position']);
                }

                if (array_key_exists('scope', $elementInformation)) {
                    $element->setScope($elementInformation['scope']);
                }

                if (array_key_exists('defaultValue', $elementInformation)) {
                    $element->setValue($elementInformation['defaultValue']);
                }
            }
        }

        $this->entityManager->flush();

        // And now write values
        foreach ($config as $nameOfBackendForm => $formElements) {
            foreach ($formElements as $elementName => $elementInformation) {
                // Load form if it is set
                if (ConfigurationDumper::NO_FORM_NAME !== $nameOfBackendForm) {
                    $form = $this->findOrCreateForm($configFormRepository, $nameOfBackendForm, $elementInformation);
                } else {
                    $form = null;
                }

                $element =
                    $this->findOrCreateElement($configElementRepository, $form, $elementName, $elementInformation);

                if (isset($elementInformation['shopValues']) && is_array($elementInformation['shopValues'])) {
                    foreach ($elementInformation['shopValues'] as $shopId => $value) {
                        $this->configWriter->save(
                            $element->getName(),
                            $value,
                            (null !== $form) ? $form->getName() : null,
                            $shopId
                        );
                    }
                }
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
     * @param string           $formName
     * @param array            $elementInformation
     *
     * @return Form
     */
    private function findOrCreateForm(ObjectRepository $configFormRepository, $formName, $elementInformation)
    {
        /**
         * @todo at the moment a deleted or not existing forms are making problems (dublicate key), which I have to investigate
         * @todo for proper creation of form we would also need the plugin (there is a relation between forms and plugins)
         * @todo look at /vendor/shopware/shopware/engine/Shopware/Models/Config/Form.php:42
         */
        $form = $configFormRepository->findOneBy(['name' => $formName]);
        if (null === $form) {
            $form = new Form();
            $this->entityManager->persist($form);
        }

        if (array_key_exists('form', $elementInformation)) {
            $form->setName($elementInformation['form']['name']);
            $form->setLabel($elementInformation['form']['label']);
            $form->setDescription($elementInformation['form']['description']);
            $form->setPosition($elementInformation['form']['position']);
        }

        return $form;
    }

    /**
     * @param ObjectRepository $configElementRepository
     * @param null|Form        $form
     * @param string           $elementName
     * @param array            $elementInformation
     *
     * @return object|Element
     */
    private function findOrCreateElement(
        ObjectRepository $configElementRepository,
        $form,
        $elementName,
        $elementInformation
    ) {
        $elementId = $this->getConfigElementIdByNameAndForm($elementName, $form);
        if (null !== $elementId) {
            $element = $configElementRepository->find($elementId);
        } else {
            $elementType = $elementInformation['type'];
            $elementOptions = (null !== $elementInformation['options']) ? $elementInformation['options'] : [];

            $element = new Element($elementType, $elementName, $elementOptions);
            $element->setForm($form);
            $this->entityManager->persist($element);
        }

        return $element;
    }

    /**
     * @param string    $name
     * @param null|Form $form
     *
     * @return int
     */
    private function getConfigElementIdByNameAndForm($name, $form)
    {
        $formId = (null !== $form) ? $form->getId() : '0';
        $sql = ' 
            SELECT `id`
                FROM `s_core_config_elements`
                WHERE `name` = :elementName AND `form_id` = :formId
        ';

        /** @var Statement $stmt */
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['elementName' => $name, 'formId' => $formId]);
        $result = $stmt->fetch();

        if (count($result) < 1) {
            return null;
        }

        return $result['id'];
    }
}
