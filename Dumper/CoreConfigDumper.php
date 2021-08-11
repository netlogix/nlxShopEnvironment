<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Shopware\Components\ConfigWriter;
use Shopware\Models\Config\Element;
use Shopware\Models\Config\Form;
use Shopware\Models\Shop\Shop;

class CoreConfigDumper implements DumperInterface
{
    const NO_FORM_NAME = '__NO_FORM__';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ConfigWriter */
    private $configWriter;

    /** @var array|Shop[] */
    private $shops;

    public function __construct(
        EntityManagerInterface $entityManager,
        ConfigWriter $configWriter
    ) {
        $this->entityManager = $entityManager;
        $this->configWriter = $configWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(): array
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
     * @return array|Shop[]
     */
    private function getShops(): array
    {
        if (null === $this->shops) {
            /** @var $repository \Shopware\Models\Shop\Repository */
            $repository = $this->entityManager->getRepository(Shop::class);
            $this->shops = $repository->findAll();
        }

        return $this->shops;
    }

    private function getFormName(?Form $backendForm): string
    {
        if (null === $backendForm) {
            return self::NO_FORM_NAME;
        }

        return $backendForm->getName();
    }

    /**
     * @param mixed   $defaultValue
     * @param mixed[] $configuration
     */
    private function addElement(Element $element, ?Form $backendForm, $defaultValue, array &$configuration): void
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
     * @param mixed[] $configuration
     */
    private function addElementInformation(Element $element, ?Form $backendForm, array &$configuration): void
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
     * @param mixed[] $configuration
     */
    private function addFormInformation(Element $element, Form $backendForm, array &$configuration): void
    {
        $configuration[$backendForm->getName()][$element->getName()]['form'] = [
            'name'        => $backendForm->getName(),
            'label'       => $backendForm->getLabel(),
            'description' => $backendForm->getDescription(),
            'position'    => $backendForm->getPosition(),
            'plugin_id'   => $backendForm->getPluginId(),
        ];
    }
}
