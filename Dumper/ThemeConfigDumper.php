<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright 2018 solutionDrive GmbH
 */

namespace sdShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Shop\Template;
use Shopware\Models\Shop\TemplateConfig\Element as ThemeElement;
use Shopware\Models\Shop\TemplateConfig\Value as ThemeElementValue;

class ThemeConfigDumper implements DumperInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
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
}
