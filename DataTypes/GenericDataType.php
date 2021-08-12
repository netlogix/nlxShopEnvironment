<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\DataTypes;

use nlxShopEnvironment\Dumper\DumperInterface;
use nlxShopEnvironment\Loader\LoaderInterface;

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
    public function getDumper(): DumperInterface
    {
        return $this->dumper;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoader(): LoaderInterface
    {
        return $this->loader;
    }
}
