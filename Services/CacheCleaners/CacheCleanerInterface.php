<?php
declare(strict_types=1);

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Services\CacheCleaners;

interface CacheCleanerInterface
{
    public function clean(): bool;
}
