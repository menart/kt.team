<?php

namespace App\Import;

use App\Dto\ProductDto;
use App\Entity\Category;
use App\Manager\CategoryManager;
use App\Manager\ProductManager;
use App\Service\AsyncService;
use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractImport
{
    private const BATCH_SIZE = 1000;

    protected CategoryManager $categoryManager;
    protected ProductManager $productManager;
    protected AsyncService $asyncService;

    private ArrayCollection $categories;
    private ArrayCollection $products;
    protected string $fileName;

    private int $count = 0;

    /**
     * @param CategoryManager $categoryManager
     * @param ProductManager $productManager
     * @param AsyncService $asyncService
     * @param string $fileName
     */
    public function __construct(
        CategoryManager        $categoryManager,
        ProductManager         $productManager,
        AsyncService           $asyncService,
        string                 $fileName
    )
    {
        $this->categoryManager = $categoryManager;
        $this->productManager = $productManager;
        $this->asyncService = $asyncService;
        $this->fileName = $fileName;
        $this->categories = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    abstract function parse(): int;

    protected function saveProduct(string $name, string $description, int $weight, string $categoryName): void
    {
        $productDto = new ProductDto();
        $productDto->setName($name);
        $productDto->setDescription($description);
        $productDto->setWeight($weight);
        $productDto->setCategory($this->findCategory($categoryName));
        $this->products->add($productDto);
        if ($this->products->count() === self::BATCH_SIZE) {
            $this->saveBatchIntoDb();
        }
    }

    private function saveBatchIntoDb(): void
    {
        if (empty($this->products)) return;
        if ($this->products->count() > 0) {
            $this->productManager->createBatch($this->products);
            unset($this->products);
        }
        $this->products = new ArrayCollection();
    }

    protected function finishImport()
    {
        $this->saveBatchIntoDb();
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

    protected function incCountUpload(): void
    {
        $this->count++;
    }
}