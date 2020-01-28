<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sdShopEnvironment\DataTypes;

interface DataTypeCollectorInterface
{
    /**
     * @param DataTypeInterface $dataType
     * @param string            $rootName
     */
    public function add(DataTypeInterface $dataType, $rootName);

    /**
     * @param string $rootName
     *
     * @return null|DataTypeInterface
     */
    public function get($rootName);

    /**
     * @return array|DataTypeInterface[]
     */
    public function getAll();
}
