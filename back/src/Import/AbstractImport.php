<?php

namespace App\Import;

use App\Dto\ProductDto;
use App\Entity\Category;
use App\Exception\NotSupportedExportFileException;
use App\Manager\CategoryManager;
use App\Manager\ProductManager;
use App\Service\AsyncService;
use DateInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Cache\InvalidArgumentException;

abstract class AbstractImport
{
    private const BATCH_SIZE = 1000;
    private const BIG_SIZE = 5 * 1024 * 1024;
    private const EXT_EXPORT = 'json';

    protected CategoryManager $categoryManager;
    protected ProductManager $productManager;
    protected ExportFactory $exportFactory;
    protected AsyncService $asyncService;

    private ArrayCollection $categories;
    private ArrayCollection $products;
    protected string $fileName;
    private int $iteration = 0;
    private bool $isBigFile;

    private int $count = 0;

    /**
     * @param CategoryManager $categoryManager
     * @param ProductManager $productManager
     * @param ExportFactory $exportFactory
     * @param AsyncService $asyncService
     * @param string $fileName
     */
    public function __construct(
        CategoryManager        $categoryManager,
        ProductManager         $productManager,
        ExportFactory          $exportFactory,
        AsyncService           $asyncService,
        string                 $fileName
    )
    {
        $this->categoryManager = $categoryManager;
        $this->productManager = $productManager;
        $this->exportFactory = $exportFactory;
        $this->asyncService = $asyncService;
        $this->fileName = $fileName;
        if (file_exists($fileName)) {
            $this->isBigFile = filesize($fileName) > self::BIG_SIZE;
        }
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
            $this->saveBatch();
        }
    }

    protected function saveBatch(): void
    {
        if (empty($this->products)) return;
        if ($this->isBigFile) {
            $this->saveIntoPartFile();
        } else {
            $this->saveIntoDB();
        }
        $this->products = new ArrayCollection();
    }

    private function saveIntoDB(): void
    {
        if ($this->products->count() > 0) {
            /** @var ProductDto $productDto */
            foreach ($this->products as &$productDto) {
                if ($productDto->getCategory() === null)
                    $productDto->setCategory($this->findCategory($productDto->getName()));
            }
            $this->productManager->createBatch($this->products);
            unset($productDto);
        }
    }

    /**
     * @throws NotSupportedExportFileException
     * @throws \JsonException
     */
    private function saveIntoPartFile(): void
    {
        $pathInfo = pathinfo($this->fileName);
        $dir = $pathInfo['dirname'];
        $name = $pathInfo['filename'];
        $fileName = rtrim($dir, '/') . DIRECTORY_SEPARATOR
            . $name . '_' . ++$this->iteration . '.' . self::EXT_EXPORT;
        $export = $this->exportFactory->getInstance($fileName);
        if ($export->save($this->products) > 0) {
            $this->asyncService->publishToExchange(
                AsyncService::PARSE_DATA_FILE,
                json_encode(['pathFile' => $fileName], JSON_THROW_ON_ERROR)
            );
        }
        unset($productDto);
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