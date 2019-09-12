<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use sdShopEnvironment\Loader\CategoryLoader;
use sdShopEnvironment\Loader\LoaderInterface;
use Shopware\Models\Category\Category;
use Shopware\Models\Search\CustomSorting;

class CategoryLoaderSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ObjectRepository $categoryRepository
    ) {
        $entityManager
            ->getRepository(Category::class)
            ->willReturn($categoryRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(CategoryLoader::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    public function it_can_load_empty(
        EntityManagerInterface $entityManager,
        ObjectRepository $categoryRepository
    ) {
        $categoryRepository->findOneBy(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load([]);
    }

    public function it_cannot_update_existing_category_if_category_was_not_found(
        EntityManagerInterface $entityManager,
        ObjectRepository $categoryRepository
    ) {
        $config = [
            'ALL' => [
                'ProductBoxLayout' => 'list',
            ],
        ];

        $categoryRepository->findOneBy(Argument::any())
            ->willReturn(null);

        $entityManager->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->flush()
            ->shouldNotBeCalled();

        $this->shouldThrow(\RuntimeException::class)
            ->during('load', [$config]);
    }

    public function it_cannot_update_existing_category_if_setter_not_exist(
        EntityManagerInterface $entityManager,
        ObjectRepository $categoryRepository,
        Category $category
    ) {
        $config = [
            'ALL' => [
                'testMethod' => 'list',
            ],
        ];

        $categoryRepository->findOneBy(Argument::any())
            ->willReturn($category);

        $entityManager->persist(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->flush()
            ->shouldNotBeCalled();

        $this->shouldThrow(\RuntimeException::class)
            ->during('load', [$config]);
    }

    public function it_can_update_existing_category(
        EntityManagerInterface $entityManager,
        ObjectRepository $categoryRepository,
        Category $category
    ) {
        $config = [
            'ALL' => [
                'ProductBoxLayout' => 'list',
            ],
        ];

        $categoryRepository->findOneBy(['name' => 'ALL'])
            ->willReturn($category);

        $category->setProductBoxLayout('list')
            ->shouldBeCalled();

        $category->setSortingIds(Argument::any())
            ->shouldNotBeCalled();

        $entityManager->persist($category)
            ->shouldBeCalled();

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($config);
    }

    public function it_can_update_existing_category_sorting(
        EntityManagerInterface $entityManager,
        ObjectRepository $categoryRepository,
        ObjectRepository $customSortingRepository,
        Category $category
    ) {
        if (\class_exists('CustomSorting')) {
            $customSorting1 = new CustomSorting();
            $customSorting2 = new CustomSorting();
        } else {
            return;
        }

        $config = [
            'ALL' => [
                'SortingIds' => [
                    'Test1',
                    'Test2',
                ],
            ],
        ];
        $sortingIdsText = '|1|2';

        $categoryRepository->findOneBy(['name' => 'ALL'])
            ->willReturn($category);

        $category->setSortingIds($sortingIdsText)
            ->shouldBeCalled();

        $entityManager->getRepository(CustomSorting::class)
            ->willReturn($customSortingRepository);

        $customSorting1->getId()
            ->willReturn(1);

        $customSortingRepository->findOneBy(['label' => 'Test1'])
            ->willReturn($customSorting1);

        $customSorting2->getId()
            ->willReturn(2);

        $customSortingRepository->findOneBy(['label' => 'Test2'])
            ->willReturn($customSorting2);

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($config);
    }

    public function it_can_copy_sortings_from_parent_to_child_categories(
        EntityManagerInterface $entityManager,
        ObjectRepository $categoryRepository,
        ObjectRepository $customSortingRepository,
        Category $category,
        Connection $connection
    ) {
        if (\class_exists('CustomSorting')) {
            $customSorting1 = new CustomSorting();
            $customSorting2 = new CustomSorting();
        } else {
            return;
        }

        $config = [
            'ALL' => [
                'SortingIds' => [
                    'Test1',
                    'Test2',
                ],
                'copyCategory' => true,
            ],
        ];

        $sortingIdsText = '|1|2';

        $categoryRepository->findOneBy(['name' => 'ALL'])
            ->willReturn($category);

        $category->setSortingIds($sortingIdsText)
            ->shouldBeCalled();

        $entityManager->getRepository(CustomSorting::class)
            ->willReturn($customSortingRepository);

        $customSorting1->getId()
            ->willReturn(1);

        $customSortingRepository->findOneBy(['label' => 'Test1'])
            ->willReturn($customSorting1);

        $customSorting2->getId()
            ->willReturn(2);

        $customSortingRepository->findOneBy(['label' => 'Test2'])
            ->willReturn($customSorting2);

        $category->getSortingIds()
            ->willReturn($sortingIdsText);

        $category->getId()
            ->willReturn(4);

        $connection->executeUpdate(
            'UPDATE s_categories SET `sorting_ids` = :sortingIds WHERE path LIKE :path',
            [
                ':sortingIds' => $sortingIdsText,
                ':path' => '%|' . 4 . '|%',
            ]
        )->shouldBeCalled();

        $entityManager->getConnection()
            ->willReturn($connection);

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load($config);
    }
}
