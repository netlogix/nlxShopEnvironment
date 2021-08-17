<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Services\Resolver;

interface Resolver
{
    /**
     * @param mixed[] $content
     *
     * @return mixed[]
     */
    public function resolve(array $content): array;
}
