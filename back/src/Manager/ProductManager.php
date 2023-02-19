<?php

declare(strict_types=1);

namespace App\Manager;

use App\Dto\FilterDto;
use App\Entity\Category;
use App\Entity\Product;
use App\Mapper\ProductMapper;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Менеджер для product entity
 */
class ProductManager
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;

    public function __construct(EntityManagerInterface $entityManager, ProductMapper $productMapper)
    {
        $this->entityManager = $entityManager;
        /* @var ProductRepository $productRepository */
        $this->productRepository = $this->entityManager->getRepository(Product::class);
    }

    public function createBatch(ArrayCollection $batchProductsDto): int
    {
        $batchProductEntity = $batchProductsDto->map(function ($value) {
            $productEntity = ProductMapper::toEntity($value);
            $this->entityManager->persist($productEntity);

            return $productEntity;
        });
        $this->entityManager->flush();
        $count = $batchProductEntity->reduce(function (int $accumulator, Product $entity): int {
            return null !== $accumulator + $entity->getId() ? 1 : 0;
        }, 0);
        $this->entityManager->clear();

        return $count;
    }

    public function create(string $name, string $description, int $weight, Category $category): Product
    {
        $product = new Product();
        $product->setName($name);
        $product->setDescription($description);
        $product->setWeight($weight);
        $product->setCategory($category);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    /**
     * @return Product[]
     * @param  int       $page
     * @param  int       $perPage
     * @param  FilterDto $filter
     */
    public function getProducts(int $page, int $perPage, FilterDto $filter): array
    {
        $expr = Criteria::expr();

        $criteria = Criteria::create();
        if ($filter->weightMin > 0) {
            $criteria->andWhere($expr->gte('weight', $filter->weightMin));
        }
        if ($filter->weightMax > 0) {
            $criteria->andWhere($expr->lte('weight', $filter->weightMax));
        }
        if (strlen(trim($filter->query)) > 0) {
            $criteria->andWhere($expr->contains('name', $filter->query));
        }
        if (count($filter->category) > 0) {
            $criteria->andWhere($expr->in('category', $filter->category));
        }
        $criteria->setFirstResult($page * $perPage);
        $criteria->setMaxResults($perPage);

        return $this->productRepository->matching($criteria)->getValues();
    }

    public function getCountPage(int $perPage): int
    {
        if (0 === $perPage) {
            return 0;
        }

        return ceil($this->productRepository->getCountProducts() / $perPage);
    }
}
