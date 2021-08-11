<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Dumper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Dumper\DumperInterface;
use nlxShopEnvironment\Dumper\SearchFieldsDumper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SearchFieldsDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        Connection $connection
    ): void {
        $entityManager->getConnection()
            ->willReturn($connection);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SearchFieldsDumper::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_search_fields(
        Connection $connection,
        Statement $statement
    ): void {
        $searchFields = [
            ['id' => 123],
        ];

        $connection->executeQuery(Argument::any())
            ->willReturn($statement);

        $statement->fetchAll()
            ->willReturn($searchFields);

        $this->dump()
            ->shouldBe($searchFields);
    }
}
