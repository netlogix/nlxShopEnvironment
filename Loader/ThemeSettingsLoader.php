<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Theme\Settings;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ThemeSettingsLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ObjectRepository */
    private $themeSettingsRepository;

    /** @var DenormalizerInterface */
    private $denormalizer;

    public function __construct(EntityManagerInterface $entityManager, DenormalizerInterface $denormalizer)
    {
        $this->entityManager = $entityManager;
        $this->themeSettingsRepository = $entityManager->getRepository(Settings::class);
        $this->denormalizer = $denormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        foreach ($config as $id => $themeSettingsData) {
            try {
                $this->importThemeSettings($id, $themeSettingsData);
            } catch (\Throwable $throwable) {
                $this->outputException($id, $throwable);
                continue;
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param string $themeSettingId
     * @param array  $themeSettingData
     */
    private function importThemeSettings($themeSettingId, $themeSettingData)
    {
        $themeSetting = $this->themeSettingsRepository->find($themeSettingId);
        if (null === $themeSetting) {
            throw new \RuntimeException('Cannot import theme setting which is not already known to shopware. Because we cannot set the id');
        }

        $this->denormalizer->denormalize($themeSettingData, Settings::class, null, ['object_to_populate' => $themeSetting]);
    }

    /**
     * @param int        $id
     * @param \Exception $exception
     */
    private function outputException($id, \Throwable $exception)
    {
        if (!\defined('PHPSPEC')) {
            echo 'Error during import of theme setting ' . $id . PHP_EOL;
            echo $exception->getMessage();
        }
    }
}
