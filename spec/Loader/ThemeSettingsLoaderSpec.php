<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Loader\LoaderInterface;
use nlxShopEnvironment\Loader\ThemeSettingsLoader;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Theme\Settings;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ThemeSettingsLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ModelRepository $themeSettingsRepository,
        DenormalizerInterface $denormalizer
    ) {
        $entityManager
            ->getRepository(Settings::class)
            ->willReturn($themeSettingsRepository);

        $this->beConstructedWith($entityManager, $denormalizer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ThemeSettingsLoader::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_cannot_load_new_theme_settings(
        EntityManagerInterface $entityManager,
        ModelRepository $themeSettingsRepository,
        DenormalizerInterface $denormalizer
    ) {
        $themeSettingsRepository
            ->find(99)
            ->willReturn(null);

        $data = [99 => ['compiler_force' => true, 'compiler_compress_js' => false]];

        $denormalizer
            ->denormalize(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->shouldNotBeCalled();

        $entityManager
            ->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager
            ->flush()
            ->shouldBeCalled();

        $this->load($data);
    }

    public function it_can_load_existing_theme_settings(
        EntityManagerInterface $entityManager,
        ModelRepository $themeSettingsRepository,
        Settings $themeSetting,
        DenormalizerInterface $denormalizer
    ) {
        $themeSettingsRepository
            ->find(1)
            ->willReturn($themeSetting);

        $data = [1 => ['compiler_force' => true, 'compiler_compress_js' => false]];

        $denormalizer
            ->denormalize(Argument::any(), Argument::any(), Argument::any(), Argument::withEntry('object_to_populate', $themeSetting->getWrappedObject()))
            ->shouldBeCalled();

        $entityManager
            ->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager
            ->flush()
            ->shouldBeCalled();

        $this->load($data);
    }
}
