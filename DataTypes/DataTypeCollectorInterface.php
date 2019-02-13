<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
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
