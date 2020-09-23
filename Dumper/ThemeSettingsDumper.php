<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Dumper;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Theme\Settings;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ThemeSettingsDumper implements DumperInterface
{
    /** @var ObjectRepository */
    private $themeSettingsRepository;

    /** @var NormalizerInterface */
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, NormalizerInterface $serializer)
    {
        $this->themeSettingsRepository = $entityManager->getRepository(Settings::class);
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $themeSettings = [];

        foreach ($this->themeSettingsRepository->findAll() as $themeSetting) {
            $themeSettings[$themeSetting->getId()] = $this->serializer->normalize($themeSetting);
        }

        return $themeSettings;
    }
}
