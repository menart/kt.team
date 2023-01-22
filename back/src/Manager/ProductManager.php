<?php

namespace App\Manager;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ProductManager
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Product::class);
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

    public function getProducts(int $page, int $perPage): array
    {
        return $this->repository->getProducts($page, $perPage);
    }
}