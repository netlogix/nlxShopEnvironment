<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Shopware\Models\Shop\Shop;
use Shopware\Models\Shop\Template;
use Shopware\Models\Shop\TemplateConfig\Element as ThemeElement;
use Shopware\Models\Shop\TemplateConfig\Value;

class ThemeConfigLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        $configElementRepository = $this->entityManager->getRepository(ThemeElement::class);
        $configTemplateRepository = $this->entityManager->getRepository(Template::class);
        $configValueRepository = $this->entityManager->getRepository(Value::class);
        $shopRepository = $this->entityManager->getRepository(Shop::class);

        foreach ($config as $themeName => $themeValues) {
            $template = $configTemplateRepository->findOneBy(['name' => $themeName]);
            if (null === $template) {
                $this->logger->warning(
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
    private function findOrCreateThemeConfig(
        ObjectRepository $configElementRepository,
        Template $template,
        $configValues
    ) {
        $element = $configElementRepository->findOneBy([
            'name' => $configValues['name'],
            'templateId' => $template->getId(),
        ]);
        if (null === $element) {
            $themeName = $template->getName();
            $elementName = $configValues['name'];
            $this->logger->warning(
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
}
