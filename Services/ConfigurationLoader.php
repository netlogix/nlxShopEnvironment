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
use Shopware\Models\Config\Form;
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
        $configFormRepository = $entityManager->getRepository('Shopware\Models\Config\Form');

        $contentOfYamlFile = Yaml::parse($pathToFile);

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
                $entityManager->persist($element);
            }
        }
        $entityManager->flush();
    }

    /**
     * @param $configFormRepository
     * @param $elementInformation
     * @return Form
     */
    private function findOrCreateForm($configFormRepository, $elementInformation)
    {
        $form = $configFormRepository->findOneBy(['name' => $elementInformation['form']['name']]);
        if (null === $form) {
            $form = new Form();
        }

        $form->setName($elementInformation['form']['name']);
        $form->setLabel($elementInformation['form']['label']);
        $form->setDescription($elementInformation['form']['description']);
        $form->setPosition($elementInformation['form']['position']);
        return $form;
    }

    private function findOrCreateElement($configElementRepository, $elementName, $elementInformation)
    {
        $element = $configElementRepository->findOneBy(['name' => $elementName]);
        if (null === $element) {
            $elementType = $elementInformation['type'];
            $elementOptions = $elementInformation['options'];

            $element = new Element($elementType, $elementName, $elementOptions);
        }
        return $element;
    }
}
