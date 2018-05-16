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
use Symfony\Component\Yaml\Yaml;

class ConfigurationLoader implements ConfigurationLoaderInterface
{
    /** @var Container */
    private $container;

    /** @var EntityManagerInterface */
    private $entityManager;

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

        $this->entityManager = $this->container->get('models');

        $configElementRepository = $this->entityManager->getRepository('Shopware\Models\Config\Element');
        $configFormRepository = $this->entityManager->getRepository('Shopware\Models\Config\Form');

        $contentOfYamlFile = Yaml::parse(file_get_contents($pathToFile));


        // @todo continue here when all data is dumped correctly
        foreach ($contentOfYamlFile as $nameOfBackendForm => $formElements) {
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
