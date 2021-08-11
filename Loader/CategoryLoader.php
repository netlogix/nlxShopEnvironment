<?php declare(strict_types=1);

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace nlxShopEnvironment\Loader;

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
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        foreach ($config as $categoryName => $categoryConfig) {
            $category = $categoryRepository->findOneBy(['name' => $categoryName]);

            if (null === $category) {
                throw new \RuntimeException(\sprintf('The loading configuration contains the category %s that is not created in the database', $categoryName));
            }
            $this->setCategoryConfig($category, $categoryConfig);

            $this->entityManager->persist($category);
        }
        $this->entityManager->flush();
    }

    /**
     * @param mixed[] $categoryConfig
     */
    private function setCategoryConfig(Category $category, array $categoryConfig): void
    {
        foreach ($categoryConfig as $parameter => $value) {
            $setter = 'set' . $parameter;

            if ($this->isParameter('copyCategory', $parameter)) {
                if (true === $value) {
                    $this->copyCategorySettings($category);
                }
                continue;
            }

            if ($this->isParameter('sortings', $parameter) || $this->isParameter('SortingIds', $parameter)) {
                $value = $this->generateSortingIds($value);
                $setter = 'setSortingIds';
            }

            if (false === \method_exists($category, $setter)) {
                throw new \RuntimeException(\sprintf('Property could not be imported as it does not exist: %s (category: %s)', $parameter, $category->getName()));
            }
            $category->$setter($value);
        }
    }

    /**
     * @param string[] $sortingNames
     */
    private function generateSortingIds(array $sortingNames): string
    {
        $sortings = $this->getSortings($sortingNames);
        $sortingIdsText = '';

        foreach ($sortings as $sorting) {
            $sortingIdsText .= '|' . $sorting->getId();
        }
        return $sortingIdsText;
    }

    private function copyCategorySettings(Category $category): void
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
    private function getSortings(array $sortingNames): array
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

    private function isParameter(
        string $searchParameter,
        string $givenParameter
    ): bool {
        return \strtolower($searchParameter) === \strtolower($givenParameter);
    }
}
