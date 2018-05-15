<?php

namespace sdShopEnvrionment\Tests;

use sdShopEnvrionment\sdShopEnvrionment as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'sdShopEnvrionment' => []
    ];

    public function testCanCreateInstance()
    {
        /** @var Plugin $plugin */
        $plugin = Shopware()->Container()->get('kernel')->getPlugins()['sdShopEnvrionment'];

        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}
