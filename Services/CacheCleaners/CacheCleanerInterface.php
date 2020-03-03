<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sdShopEnvironment\Services\CacheCleaners;

interface CacheCleanerInterface
{
    public function clean(): bool;
}
