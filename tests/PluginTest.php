<?php

namespace sdShopEnvironment\Tests;

use sdShopEnvironment\sdShopEnvironment as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'sdShopEnvironment' => []
    ];

    public function testCanCreateInstance()
    {
        /** @var Plugin $plugin */
        $plugin = Shopware()->Container()->get('kernel')->getPlugins()['sdShopEnvironment'];

        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}
