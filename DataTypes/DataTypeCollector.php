<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\DataTypes;

class DataTypeCollector implements DataTypeCollectorInterface
{
    /** @var DataTypeInterface[] */
    private $types = [];

    /**
     * {@inheritdoc}
     */
    public function add(DataTypeInterface $dataType, $rootName): void
    {
        $this->types[$rootName] = $dataType;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $rootName): ?DataTypeInterface
    {
        if (\array_key_exists($rootName, $this->types)) {
            return $this->types[$rootName];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): array
    {
        return $this->types;
    }
}
