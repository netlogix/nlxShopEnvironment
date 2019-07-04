<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Loader;

use Doctrine\Common\Persistence\ObjectRepository;
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

        $entityManager->flush()
            ->shouldBeCalled();

        $this->load([]);
    }

    public function it_can_update_existing_category(
        EntityManagerInterface $entityManager,
        ObjectRepository $categoryRepository,
        ObjectRepository $customSortingRepository,
        Category $category1
    ) {
        if (class_exists('CustomSorting')) {
            $customSorting1 = new CustomSorting();
            $customSorting2 = new CustomSorting();
        } else {
            return;
        }

        $config = [
            'ALL' => [
                'sortings' => [
                    'Test1',
                    'Test2',
                ],
            ],
        ];
        $sortingIdsText = '|1|2';

        $categoryRepository->findOneBy(['name' => 'ALL'])
            ->willReturn($category1);

        $category1->setSortingIds($sortingIdsText)
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
}
