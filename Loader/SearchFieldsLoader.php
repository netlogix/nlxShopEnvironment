<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Loader;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class SearchFieldsLoader implements LoaderInterface
{
    /** @var Connection */
    private $connection;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->connection = $entityManager->getConnection();
    }

    /**
     * {@inheritdoc}
     */
    public function load(?array $config): void
    {
        if (empty($config) || !\is_array($config)) {
            return;
        }
        $sqlStatements = ['DELETE FROM `s_search_fields` WHERE 1;'];
        foreach ($config as $searchField) {
            $sqlStatements[] = \sprintf(
                'INSERT INTO `s_search_fields` (%s) VALUES (%s);',
                \implode(',', \array_keys($searchField)),
                \implode(',', \array_map([$this->connection, 'quote'], $searchField))
            );
        }
        $this->connection->exec(\implode('', $sqlStatements));
    }
}
