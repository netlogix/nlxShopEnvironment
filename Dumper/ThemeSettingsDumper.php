<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Theme\Settings;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ThemeSettingsDumper implements DumperInterface
{
    /** @var ModelRepository */
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
