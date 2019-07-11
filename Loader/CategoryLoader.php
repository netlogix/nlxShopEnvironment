<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Category\Category;
use Shopware\Models\Search\CustomSorting;

class CategoryLoader implements LoaderInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function load($config)
    {
        foreach ($config as $categoryName => $categoryData) {
            $categoryRepository = $this->entityManager->getRepository(Category::class);
            $category = $categoryRepository->findOneBy(['name' => $categoryName]);

            if (null === $category) {
                throw new \RuntimeException(\sprintf('The loading configuration contains the category %s that is not created in the database', $categoryName));
            }
            $sortingIdsText = $this->generateSortingIds($categoryData['sortings']);
            $category->setSortingIds($sortingIdsText);

            if (isset($categoryData['copyCategory']) && true === $categoryData['copyCategory']) {
                $this->copyCategorySettings($category);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @param string[] $sortingNames
     */
    private function generateSortingIds(array $sortingNames)
    {
        $sortings = $this->getSortings($sortingNames);
        $sortingIdsText = '';

        foreach ($sortings as $sorting) {
            $sortingIdsText = $sortingIdsText . '|' . $sorting->getId();
        }
        return $sortingIdsText;
    }

    private function copyCategorySettings(Category $category)
    {
        $this->entityManager->getConnection()->executeUpdate(
            'UPDATE s_categories SET `sorting_ids` = :sortingIds WHERE path LIKE :path',
            [
                ':sortingIds' => (string) $category->getSortingIds(),
                ':path' => '%|' . $category->getId() . '|%',
            ]
        );
    }

    /**
     * @param string[] $sortingNames
     *
     * @return CustomSorting[]
     */
    private function getSortings($sortingNames)
    {
        $sortingRepository = $this->entityManager->getRepository(CustomSorting::class);
        $sortings = [];
        foreach ($sortingNames as $sortingName) {
            $sorting = $sortingRepository->findOneBy(['label' => $sortingName]);

            if (null === $sorting) {
                throw new \RuntimeException(\sprintf('The loading configuration contains the sorting %s that is not created in the database', $sortingName));
            }
            $sortings[] = $sorting;
        }
        return $sortings;
    }
}
