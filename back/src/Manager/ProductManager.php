<?php

namespace App\Manager;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductManager
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
     * @param int $page
     * @param int $perPage
     * @return Product[]
     */
    public function getProducts(int $page = 0, int $perPage = 20): array
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);
        return $productRepository->getProducts($page, $perPage);
    }
}