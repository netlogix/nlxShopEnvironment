<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Shopware\Components\Model\ModelRepository;
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
    public function load(array $config): void
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
     * @param mixed[] $configValues
     *
     * @return ThemeElement|null|object
     */
    private function findOrCreateThemeConfig(
        ModelRepository $configElementRepository,
        Template $template,
        array $configValues
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
     * @param mixed $configValue
     */
    private function findOrCreateThemeConfigValue(
        ModelRepository $configValueRepository,
        ThemeElement $element,
        Shop $shop,
        $configValue
    ): Value {
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
