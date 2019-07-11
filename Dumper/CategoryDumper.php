<?php

/*
 * Created by solutionDrive GmbH
 *
 * @copyright solutionDrive GmbH
 */

namespace sdShopEnvironment\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use Shopware\Models\Category\Category;
use Shopware\Models\Search\CustomSorting;

class CategoryDumper implements DumperInterface
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
    public function dump()
    {
        $config = [];

        $categoryRepository = $this->entityManager->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        /** @var Category $category */
        foreach ($categories as $category) {
            $config[$category->getName()] = [
                'sortings' =>  $this->getSortings($category),
            ];
        }

        return $config;
    }

    /**
     * @return string[]
     */
    private function getSortings(Category $category)
    {
        $sortingIdsText = $category->getSortingIds();

        if (null === $sortingIdsText) {
            return [];
        }
        $sortingsName = [];
        $sortingIds = \explode('|', $sortingIdsText);
        $customSortingRepository = $this->entityManager->getRepository(CustomSorting::class);

        foreach ($sortingIds as $sortingId) {
            $customSorting = $customSortingRepository->find($sortingId);
            if (null !== $customSorting) {
                $sortingsName[] = $customSorting->getLabel();
            }
        }

        return $sortingsName;
    }
}