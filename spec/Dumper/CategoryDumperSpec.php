<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace spec\nlxShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use nlxShopEnvironment\Dumper\CategoryDumper;
use nlxShopEnvironment\Dumper\DumperInterface;
use PhpSpec\ObjectBehavior;
use Shopware\Components\Model\ModelRepository;
use Shopware\Models\Category\Category;
use Shopware\Models\Search\CustomSorting;

class CategoryDumperSpec extends ObjectBehavior
{
    public function let(
        EntityManagerInterface $entityManager,
        ModelRepository $categoryRepository
    ): void {
        $entityManager
            ->getRepository(Category::class)
            ->willReturn($categoryRepository);

        $this->beConstructedWith($entityManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CategoryDumper::class);
    }

    public function it_implements_correct_interface(): void
    {
        $this->shouldImplement(DumperInterface::class);
    }

    public function it_can_dump_empty_category(
        ModelRepository $categoryRepository
    ): void {
        $categoryRepository->findAll()
            ->shouldBeCalled()
            ->willReturn([]);

        $this->dump()
            ->shouldBe([]);
    }

    public function it_can_dump_category(
        EntityManagerInterface $entityManager,
        ModelRepository $categoryRepository,
        ModelRepository $customSortingRepository,
        Category $category
    ): void {
        if (\class_exists('CustomSorting')) {
            $customSorting1 = new CustomSorting();
            $customSorting2 = new CustomSorting();
        } else {
            return;
        }

        $category->getProductBoxLayout()
            ->willReturn('testLayout');

        $this->prepareParametersForDump(
            $entityManager,
            $categoryRepository,
            $customSortingRepository,
            $category,
            $customSorting1,
            $customSorting2
        );

        $this->dump()
            ->shouldBeLike([
                'ALL' => [
                    'productBoxLaxout' => 'testLayout',
                    'sortings' => [
                        'Test1',
                        'Test2',
                    ],
                ],
            ]);
    }

    private function prepareParametersForDump(
        EntityManagerInterface $entityManager,
        ModelRepository $categoryRepository,
        ModelRepository $customSortingRepository,
        Category $category1,
        CustomSorting $customSorting1,
        CustomSorting $customSorting2
    ): void {
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
