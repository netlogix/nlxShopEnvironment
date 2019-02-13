<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\DataTypes;

class DataTypeCollector implements DataTypeCollectorInterface
{
    /** @var DataTypeInterface[] */
    private $types = [];

    /**
     * {@inheritdoc}
     */
    public function add(DataTypeInterface $dataType, $rootName)
    {
        $this->types[$rootName] = $dataType;
    }

    /**
     * {@inheritdoc}
     */
    public function get($rootName)
    {
        if (\array_key_exists($rootName, $this->types)) {
            return $this->types[$rootName];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->types;
    }
}
