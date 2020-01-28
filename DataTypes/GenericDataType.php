<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sdShopEnvironment\DataTypes;

use sdShopEnvironment\Dumper\DumperInterface;
use sdShopEnvironment\Loader\LoaderInterface;

class GenericDataType implements DataTypeInterface
{
    /** @var DumperInterface */
    private $dumper;

    /** @var LoaderInterface */
    private $loader;

    public function __construct(DumperInterface $dumper, LoaderInterface $loader)
    {
        $this->dumper = $dumper;
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function getDumper()
    {
        return $this->dumper;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoader()
    {
        return $this->loader;
    }
}
