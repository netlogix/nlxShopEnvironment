<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Dumper;

interface DumperInterface
{
    /**
     * @return array|mixed[]
     */
    public function dump(): array;
}
