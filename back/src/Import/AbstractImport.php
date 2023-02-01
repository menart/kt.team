<?php

namespace App\Import;

use App\Dto\ProductDto;
use App\Entity\Category;
use App\Manager\CategoryManager;
use App\Manager\ProductManager;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

abstract class AbstractImport
{
    private const BATCH_SIZE = 10000;

    protected CategoryManager $categoryManager;
    protected ProductManager $productManager;
    protected CacheItemPoolInterface $cacheItemPool;

    private ArrayCollection $categories;
    private ArrayCollection $products;

    private int $count = 0;

    /**
     * @param CategoryManager $categoryManager
     * @param ProductManager $productManager
     * @param CacheItemPoolInterface $cacheItemPool
     */
    public function __construct(
        CategoryManager        $categoryManager,
        ProductManager         $productManager,
        CacheItemPoolInterface $cacheItemPool
    )
    {
        $this->categoryManager = $categoryManager;
        $this->productManager = $productManager;
        $this->cacheItemPool = $cacheItemPool;
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
            $countItem = $this->cacheItemPool->getItem('uploads.count');
            $countItem->set($this->count);
            $countItem->expiresAfter(60);
            $this->cacheItemPool->save($countItem);
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

    /**
     * @throws InvalidArgumentException
     */
    protected function incCountUpload(): void
    {
        $this->count++;
    }
}