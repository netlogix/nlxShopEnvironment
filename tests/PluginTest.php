<?php

namespace nlxShopEnvironment\Tests;

use nlxShopEnvironment\nlxShopEnvironment as Plugin;
use Shopware\Components\Test\Plugin\TestCase;

class PluginTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'nlxShopEnvironment' => []
    ];

    public function testCanCreateInstance()
    {
        /** @var Plugin $plugin */
        $plugin = Shopware()->Container()->get('kernel')->getPlugins()['nlxShopEnvironment'];

        $this->assertInstanceOf(Plugin::class, $plugin);
    }
}
