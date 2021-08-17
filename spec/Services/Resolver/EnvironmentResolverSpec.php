<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Services\Resolver;

use nlxShopEnvironment\Services\Resolver\EnvironmentResolver;
use nlxShopEnvironment\Services\Resolver\Resolver;
use PhpSpec\ObjectBehavior;

class EnvironmentResolverSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(EnvironmentResolver::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(Resolver::class);
    }

    public function it_should_resolve_environments_in_nested_array(): void
    {
        $environments = [
            'W_FATHER' => 'Thomas Wayne',
            'W_MOTHER' => 'Martha Wayne',
            'W_ALIAS'  => 'Batman',
            'G_FATHER' => 'James Gordon',
            'G_MOTHER' => 'Barbara Eileen Gordon',
            'G_ALIAS'  => 'Batgirl',
        ];
        $this->setEnvironments($environments);

        $content = [
            [
                'name' => 'Bruce Wayne',
                'family' => [
                    'father' => '%env(W_FATHER)',
                    'mother' => '%env(W_MOTHER)',
                ],
                'alias' => '%env(W_ALIAS)',
            ],
            [
                'name' => 'Barbara Gordon',
                'family' => [
                    'father' => '%env(G_FATHER)',
                    'mother' => '%env(G_MOTHER)',
                ],
                'alias' => '%env(G_ALIAS)',
            ],
        ];
        $expected = [
            [
                'name' => 'Bruce Wayne',
                'family' => [
                    'father' => 'Thomas Wayne',
                    'mother' => 'Martha Wayne',
                ],
                'alias' => 'Batman',
            ],
            [
                'name' => 'Barbara Gordon',
                'family' => [
                    'father' => 'James Gordon',
                    'mother' => 'Barbara Eileen Gordon',
                ],
                'alias' => 'Batgirl',
            ],
        ];

        $this->resolve($content)
            ->shouldBe($expected);

        $this->removeEnvironments($environments);
    }

    public function it_should_replace_placeholder_with_empty_string_if_environment_not_exist(): void
    {
        $content = [
            [
                'name' => 'Bruce Wayne',
                'alias' => '%env(W_ALIAS)',
            ],
            [
                'name' => 'Barbara Gordon',
                'alias' => '%env(G_ALIAS)',
            ],
        ];
        $expected = [
            [
                'name' => 'Bruce Wayne',
                'alias' => '',
            ],
            [
                'name' => 'Barbara Gordon',
                'alias' => '',
            ],
        ];

        $this->resolve($content)
            ->shouldBe($expected);
    }

    /**
     * @param string[] $environments
     */
    private function setEnvironments(array $environments): void
    {
        foreach ($environments as $key => $value) {
            \putenv($key . '=' . $value);
        }
    }

    /**
     * @param string[] $environments
     */
    private function removeEnvironments(array $environments): void
    {
        foreach ($environments as $key => $value) {
            \putenv($key);
        }
    }
}
