<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Loader;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Loader\LoaderInterface;
use sdShopEnvironment\Loader\SearchFieldsLoader;

class SearchFieldsLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        Connection $connection
    ) {
        $entityManager->getConnection()
            ->willReturn($connection);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(SearchFieldsLoader::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_can_load_search_fields(
        Connection $connection
    ) {
        $searchFields = [
            ['id' => 1234],
        ];

        $connection->quote(1234)
            ->shouldBeCalled()
            ->willReturn('1234');

        $connection->exec(Argument::any())
            ->shouldBeCalled();

        $this->load($searchFields);
    }

    public function it_wont_delete_existing_search_fields_on_null_argument(
        Connection $connection
    ) {
        $searchFields = null;

        $connection->exec(Argument::any())
            ->shouldNotBeCalled();

        $this->load($searchFields);
    }

    public function it_wont_delete_existing_search_fields_on_empty_array_as_argument(
        Connection $connection
    ) {
        $searchFields = [];

        $connection->exec(Argument::any())
            ->shouldNotBeCalled();

        $this->load($searchFields);
    }
}
