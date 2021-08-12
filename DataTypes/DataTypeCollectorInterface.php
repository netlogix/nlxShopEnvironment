<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\DataTypes;

interface DataTypeCollectorInterface
{
    public function add(DataTypeInterface $dataType, string $rootName): void;

    public function get(string $rootName): ?DataTypeInterface;

    /**
     * @return array|DataTypeInterface[]
     */
    public function getAll(): array;
}
