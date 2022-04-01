<?php
declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Loader;

use nlxShopEnvironment\Services\AclRoles\AclRolesImporterInterface;

class AclRolesLoader implements LoaderInterface
{
    /** @var AclRolesImporterInterface */
    private $importer;

    public function __construct(AclRolesImporterInterface $importer)
    {
        $this->importer = $importer;
    }

    /**
     * {@inheritdoc}
     */
    public function load(?array $config): void
    {
        if (null === $config) {
            return;
        }

        foreach ($config as $name => $data) {
            try {
                $this->importer->import($name, $data);
            } catch (\Throwable $throwable) {
                $this->outputException($name, $throwable);
                continue;
            }
        }
    }

    private function outputException(string $name, \Throwable $throwable): void
    {
        if (false === \defined('PHPSPEC')) {
            echo 'Error during import of role' . $name . PHP_EOL;
            echo $throwable->getMessage();
        }
    }
}
