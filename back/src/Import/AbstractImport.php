<?php

namespace App\Import;

use App\Dto\ProductDto;
use App\Entity\Category;
use App\Manager\CategoryManager;
use App\Manager\ProductManager;
use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractImport
{
    private const BATCH_SIZE = 1000;

    protected CategoryManager $categoryManager;
    protected ProductManager $productManager;

    private ArrayCollection $categories;
    private ArrayCollection $products;

    /**
     * @param CategoryManager $categoryManager
     * @param ProductManager $productManager
     */
    public function __construct(CategoryManager $categoryManager, ProductManager $productManager)
    {
        $this->categoryManager = $categoryManager;
        $this->productManager = $productManager;
        $this->categories = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    abstract function parse(string $fileName): int;

    protected function saveProduct(string $name, string $description, int $weight, string $categoryName): void
    {
        $productDto = new ProductDto();
        $productDto->setName($name);
        $productDto->setDescription($description);
        $productDto->setWeight($weight);
        $productDto->setCategory($this->findCategory($categoryName));
        $this->products->add($productDto);
        if ($this->products->count() === self::BATCH_SIZE) {
            $this->productManager->createBatch($this->products);
            $this->products = new ArrayCollection();
        }
    }

    private function findCategory(string $categoryName): Category
    {
        /** @var Category $category */
        $category = $this->categories->findFirst(function (int $key, Category $value) use ($categoryName) {
            return $value->getName() == $categoryName;
        });
        if (empty($category)) {
            $category = $this->categoryManager->getOrCreate($categoryName);
        }
        return $category;
    }
}