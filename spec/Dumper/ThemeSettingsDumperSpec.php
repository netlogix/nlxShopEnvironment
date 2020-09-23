<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Dumper;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use nlxShopEnvironment\Dumper\DumperInterface;
use nlxShopEnvironment\Dumper\ThemeSettingsDumper;
use Shopware\Models\Theme\Settings;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ThemeSettingsDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ObjectRepository $themeSettingsRepository,
        NormalizerInterface $normalizer
    ) {
        $entityManager
            ->getRepository(Settings::class)
            ->willReturn($themeSettingsRepository);

        $this->beConstructedWith($entityManager, $normalizer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ThemeSettingsDumper::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_empty_theme_settings(ObjectRepository $themeSettingsRepository)
    {
        $themeSettingsRepository
            ->findAll()
            ->shouldBeCalled()
            ->willReturn([]);

        $this
            ->dump()
            ->shouldBe([]);
    }

    public function it_can_dump_theme_settings(
        ObjectRepository $themeSettingsRepository,
        Settings $themeSettingsOne,
        Settings $themeSettingsTwo,
        NormalizerInterface $normalizer
    ) {
        $themeSettingsOne
            ->getId()
            ->willReturn(1);

        $themeSettingsTwo
            ->getId()
            ->willReturn(2);

        $themeSettingsRepository
            ->findAll()
            ->shouldBeCalled()
            ->willReturn([$themeSettingsOne, $themeSettingsTwo]);

        $normalizer
            ->normalize(Argument::type(Settings::class))
            ->shouldBeCalledTimes(2)
            ->willReturn(['data' => 'data']);

        $this
            ->dump()
            ->shouldBeLike([
                1 => ['data' => 'data'],
                2 => ['data' => 'data'],
            ]);
    }
}
