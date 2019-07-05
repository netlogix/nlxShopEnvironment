<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace spec\sdShopEnvironment\Dumper;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use sdShopEnvironment\Dumper\CategoryDumper;
use sdShopEnvironment\Dumper\DumperInterface;
use Shopware\Models\Category\Category;
use Shopware\Models\Search\CustomSorting;

class CategoryDumperSpec extends ObjectBehavior
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
        $this->shouldHaveType(CategoryDumper::class);
    }

    public function it_implements_correct_interface()
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_empty_category(
        ObjectRepository $categoryRepository
    ) {
        $categoryRepository->findAll()
            ->shouldBeCalled()
            ->willReturn([]);

        $this->dump()
            ->shouldBe([]);
    }

    public function it_can_dump_category(
        EntityManagerInterface $entityManager,
        ObjectRepository $categoryRepository,
        ObjectRepository $customSortingRepository,
        Category $category1
    ) {
        if (\class_exists('CustomSorting')) {
            $customSorting1 = new CustomSorting();
            $customSorting2 = new CustomSorting();
        } else {
            return;
        }

        $this->prepareParametersForDump(
            $entityManager,
            $categoryRepository,
            $customSortingRepository,
            $category1,
            $customSorting1,
            $customSorting2
        );

        $this->dump()
            ->shouldBeLike([
                'ALL' => [
                    'sortings' => [
                        'Test1',
                        'Test2',
                    ],
                ],
            ]);
    }

    private function prepareParametersForDump(
        EntityManagerInterface $entityManager,
        ObjectRepository $categoryRepository,
        ObjectRepository $customSortingRepository,
        Category $category1,
        CustomSorting $customSorting1,
        CustomSorting $customSorting2
    ) {
        $customSorting2->getLabel()
            ->willReturn('Test2');

        $customSortingRepository->find('2')
            ->willReturn($customSorting2);

        $customSorting1->getLabel()
            ->willReturn('Test1');

        $customSortingRepository->find('1')
            ->willReturn($customSorting1);

        $customSortingRepository->find('')
            ->willReturn(null);

        $entityManager->getRepository(CustomSorting::class)
            ->willReturn($customSortingRepository);

        $category1->getSortingIds()
            ->willReturn('|1|2');

        $category1->getName()
            ->willReturn('ALL');

        $categoryRepository->findAll()
            ->willReturn([$category1]);
    }
}
