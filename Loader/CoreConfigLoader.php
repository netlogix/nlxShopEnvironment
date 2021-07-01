<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Loader;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\ConfigWriter;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Config\Element;
use Shopware\Models\Config\Form;

class CoreConfigLoader implements LoaderInterface
{
    const NO_FORM_NAME = '__NO_FORM__';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ConfigWriter */
    private $configWriter;

    /** @var Connection */
    private $connection;

    public function __construct(
        Connection $connection,
        EntityManagerInterface $entityManager,
        ConfigWriter $configWriter
    ) {
        $this->connection = $connection;
        $this->entityManager = $entityManager;
        $this->configWriter = $configWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        $configElementRepository = $this->entityManager->getRepository('Shopware\Models\Config\Element');
        $configFormRepository = $this->entityManager->getRepository('Shopware\Models\Config\Form');

        // First load config elements and forms (structure and defaults)
        foreach ($config as $nameOfBackendForm => $formElements) {
            foreach ($formElements as $elementName => $elementInformation) {
                // Load form if it is set
                if (self::NO_FORM_NAME !== $nameOfBackendForm) {
                    $form = $this->findOrCreateForm($configFormRepository, $nameOfBackendForm, $elementInformation);
                    if (null === $form) {
                        continue;
                    }
                } else {
                    $form = null;
                }

                $element =
                    $this->findOrCreateElement($configElementRepository, $form, $elementName, $elementInformation);

                if (null !== $form) {
                    $element->setForm($form);
                }

                if (\array_key_exists('label', $elementInformation)) {
                    $element->setLabel($elementInformation['label']);
                }

                if (\array_key_exists('description', $elementInformation)) {
                    $element->setDescription($elementInformation['description']);
                }

                if (\array_key_exists('position', $elementInformation)) {
                    $element->setPosition($elementInformation['position']);
                }

                if (\array_key_exists('scope', $elementInformation)) {
                    $element->setScope($elementInformation['scope']);
                }

                if (\array_key_exists('defaultValue', $elementInformation)) {
                    $element->setValue($elementInformation['defaultValue']);
                }
            }
        }

        $this->entityManager->flush();

        // And now write values
        foreach ($config as $nameOfBackendForm => $formElements) {
            foreach ($formElements as $elementName => $elementInformation) {
                // Load form if it is set
                if (self::NO_FORM_NAME !== $nameOfBackendForm) {
                    $form = $this->findOrCreateForm($configFormRepository, $nameOfBackendForm, $elementInformation);
                    if (null === $form) {
                        continue;
                    }
                } else {
                    $form = null;
                }

                $element =
                    $this->findOrCreateElement($configElementRepository, $form, $elementName, $elementInformation);

                if (isset($elementInformation['shopValues']) && \is_array($elementInformation['shopValues'])) {
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
     * @param ModelRepository $configFormRepository
     * @param string          $formName
     * @param array           $elementInformation
     *
     * @return Form|null
     */
    private function findOrCreateForm(ModelRepository $configFormRepository, $formName, $elementInformation)
    {
        /**
         * @todo at the moment a deleted or not existing forms are making problems (dublicate key), which I have to investigate
         * @todo for proper creation of form we would also need the plugin (there is a relation between forms and plugins)
         * @todo look at /vendor/shopware/shopware/engine/Shopware/Models/Config/Form.php:42
         */
        /** @var Form|null $form */
        $form = $configFormRepository->findOneBy(['name' => $formName]);

        if (\array_key_exists('form', $elementInformation)) {
            if (null === $form) {
                $form = new Form();
                $this->entityManager->persist($form);
            }
            $this->updateFormData($form, $elementInformation);
        }

        return $form;
    }

    /**
     * @param Form       $form
     * @param string[][] $elementInformation
     */
    private function updateFormData(Form $form, array $elementInformation)
    {
        $form->setName($elementInformation['form']['name']);
        $form->setLabel($elementInformation['form']['label']);
        $form->setDescription($elementInformation['form']['description']);
        $form->setPosition($elementInformation['form']['position']);
    }

    /**
     * @param ModelRepository $configElementRepository
     * @param null|Form       $form
     * @param string          $elementName
     * @param array           $elementInformation
     *
     * @return object|Element
     */
    private function findOrCreateElement(
        ModelRepository $configElementRepository,
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

        if (\count($result) < 1) {
            return null;
        }

        return $result['id'];
    }
}
