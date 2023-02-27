<?php

declare(strict_types=1);

namespace UnitTests;

use App\Entity\Category;
use App\Entity\ImportFile;
use App\Entity\Product;
use App\Manager\ImportFileManager;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use PHPUnit\Framework\TestCase;

class AbstractTestCase extends TestCase
{
    /** @var ArrayCollection<Category> */
    protected ArrayCollection $categories;
    /** @var Category[] */
    protected array $persistCategories = [];
    /** @var ArrayCollection<Product> */
    protected ArrayCollection $products;
    /** @var Product[] */
    protected array $persistProducts = [];

    /** @var ArrayCollection<ImportFile> */
    protected ArrayCollection $importFiles;
    /** @var ImportFile[] */
    protected array $persistImportFiles = [];

    protected ImportFileManager $importFileManager;

    protected EntityRepository $categoryRepository;
    protected ProductRepository $productRepository;

    public function setUp(): void
    {
        $this->categories = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->importFiles = new ArrayCollection();
    }

    /**
     * @throws Exception
     */
    protected function makeFakeCategoryManager(): EntityManagerInterface
    {
        $categoryManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $categoryManager
            ->method('getRepository')
            ->with(Category::class)
            ->willReturn($this->makeFakeCategoryRepository());

        $categoryManager
            ->method('persist')
            ->willReturnCallback(function (Category $category): void {
                if ($category->getId() === null) {
                    $id = $this->getCategories()->count() + 1;
                    $category->setId($id);
                    $this->persistCategories[] = $category;
                }
            });

        $categoryManager
            ->method('flush')
            ->willReturnCallback(function (): void {
                foreach ($this->persistCategories as $category) {
                    $this->categories->add($category);
                }
                $this->persistCategories = [];
            });

        return $categoryManager;
    }

    /**
     * @throws Exception
     */
    protected function makeFakeProductManager(): EntityManagerInterface
    {
        $productManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productManager
            ->method('getRepository')
            ->with(Product::class)
            ->willReturn($this->makeFakeProductRepository());

        $productManager
            ->method('persist')
            ->willReturnCallback(function (Product $product): void {
                if ($product->getId() === null) {
                    $id = $this->getProducts()->count() + count($this->persistProducts) + 1;
                    $product->setId($id);
                    $this->persistProducts[] = $product;
                }
            });

        $productManager
            ->method('flush')
            ->willReturnCallback(function (): void {
                foreach ($this->persistProducts as $product) {
                    $this->products->add($product);
                }
                $this->persistProducts = [];
            });

        return $productManager;
    }

    /**
     * @throws Exception
     */
    protected function makeFakeCategoryRepository(): EntityRepository
    {
        $categoryRepository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $categoryRepository
            ->expects($this->any())
            ->method('findBy')
            ->with()
            ->willReturn($this->getCategories()->toArray());

        $categoryRepository
            ->expects($this->any())
            ->method('findOneBy')
            ->willReturnCallback(function ($criteria): ?Category {
                foreach ($this->categories as $category) {
                    if ($category->getName() === $criteria['name']) {
                        return $category;
                    }
                }
                return null;
            });

        $this->categoryRepository = $categoryRepository;
        return $categoryRepository;
    }

    /**
     * @throws Exception
     */
    protected function makeFakeProductRepository(): ProductRepository
    {
        $productRepository = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productRepository
            ->method('getCountProducts')
            ->with()
            ->willReturn($this->getProducts()->count());

        $productRepository
            ->method('matching')
            ->willReturnCallback(function ($criteria) {
                return $this->getProducts()->matching($criteria);
            });

        $this->productRepository = $productRepository;
        return $productRepository;
    }

    /**
     * @throws Exception
     */
    protected function makeFakeCategories()
    {
        for ($i = 1; $i < 6; $i++) {
            $this->categories->add((new Category())
                ->setId($i)
                ->setName(sprintf('category %d', $i))
                ->setCreatedAt(new DateTime(sprintf('2022-02-20 22:00:%02d', $i))));
        }
    }

    /**
     * @throws Exception
     */
    protected function makeFakeProducts()
    {
        for ($i = 1; $i < 50; $i++) {
            $this->products->add((new Product())
                ->setId($i)
                ->setName(sprintf('product name %d', $i))
                ->setDescription(sprintf('description test %d', $i))
                ->setWeight($i * 100)
                ->setCategory($this->getCategories()[$i % 4])
                ->setCreatedAt(new DateTime(sprintf('2022-02-20 23:00:%02d', $i))));
        }
    }

    /**
     * @return ArrayCollection<Category>
     * @throws Exception
     */
    public function getCategories(): ArrayCollection
    {
        return $this->categories;
    }

    /**
     * @return ArrayCollection<Product>
     * @throws Exception
     */
    public function getProducts(): ArrayCollection
    {
        return $this->products;
    }

    /**
     * @throws Exception
     */
    protected function makeFakeImportFiles()
    {
        $this->importFiles->clear();
        for ($i = 1; $i < 15; $i++) {
            $importFiles =
                (new ImportFile())
                    ->setId($i)
                    ->setName(sprintf('import file %d', $i))
                    ->setHash(md5(sprintf('hash %d', $i)))
                    ->setUploadAt(new DateTime(sprintf('2022-02-20 23:00:%02d', $i)))
                    ->setFinishAt()
                    ->setCountRecord($i);
            $this->importFiles->add($importFiles);
        }
    }

    protected function makeFakeImportFileManager(): EntityManagerInterface
    {
        $importFileManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importFileManager->method('getRepository')
            ->with(ImportFile::class)
            ->willReturn($this->makeFakeImportFileRepository());

        $importFileManager->method('persist')
            ->willReturnCallback(function (ImportFile $importFile): void {
                if ($importFile->getId() === null) {
                    $id = $this->importFiles->count() + 1;
                    $importFile->setId($id);
                    $this->persistImportFiles[] = $importFile;
                }
            });

        $importFileManager->method('find')
            ->willReturnCallback(function ($clazz, $id):ImportFile {
                $importFile = $this->importFiles->findFirst(function (int $key, ImportFile $importFile) use ($id) {
                    return $importFile->getId() === $id;
                });
                $this->importFiles->removeElement($importFile);
                $this->persistImportFiles[] = $importFile;
                return $importFile;
            });

        $importFileManager->method('flush')
            ->willReturnCallback(function (): void {
                foreach ($this->persistImportFiles as $importFile) {
                    $this->importFiles->add($importFile);
                }
                $this->persistImportFiles = [];
            });

        return $importFileManager;
    }

    protected function makeFakeImportFileRepository(): EntityRepository
    {
        $importFileRepository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importFileRepository->method('findBy')
            ->willReturnCallback(function ($empty, $order, $count, $offset): array {
                $criteria = new Criteria();
                $criteria->orderBy($order)->setMaxResults($count)->setFirstResult($offset);
                return $this->importFiles->matching($criteria)->toArray();
            });

        $importFileRepository->method('findOneBy')
            ->willReturnCallback(function ($find): ?ImportFile {
                return $this->importFiles->findFirst(function (int $key, ImportFile $importFile) use ($find) {
                    return $importFile->getHash() === $find['hash'];
                });
            });

        return $importFileRepository;
    }
}
