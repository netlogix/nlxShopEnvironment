<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sdShopEnvironment\Services\CacheCleaners;

class OpcacheCleaner implements CacheCleanerInterface
{
    public function clean(): bool
    {
        if (\function_exists('opcache_reset') && \extension_loaded('Zend OPcache')) {
            return \opcache_reset();
        }
        return false;
    }
}
