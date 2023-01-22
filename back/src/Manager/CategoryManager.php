<?php

namespace App\Manager;


use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CategoryManager
{
    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Category::class);
    }

    public function create(string $name): Category
    {
        $category = new Category();
        $category->setName($name);
        $this->entityManager->persist($category);
        $this->entityManager->flush();
        return $category;
    }

    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function getOrCreate(string $name)
    {
        $category = $this->repository->findBy(['name', $name]);
        return $category ?: $this->create($name);
    }
}