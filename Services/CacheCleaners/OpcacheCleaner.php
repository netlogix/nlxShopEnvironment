<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
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
