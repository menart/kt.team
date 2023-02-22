<?php

declare(strict_types=1);

namespace UnitTests\Manager;

use App\Dto\FilterDto;
use App\Dto\ProductDto;
use App\Entity\Category;
use App\Entity\Product;
use App\Manager\ProductManager;
use App\Mapper\ProductMapper;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use UnitTests\AbstractTestCase;

class ProductManagerTest extends AbstractTestCase
{
    private ProductManager $productManager;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->makeFakeCategories();
        $this->makeFakeProducts();
        $this->productManager = new ProductManager($this->makeFakeProductManager(), new ProductMapper());
    }

    public function testCountPage()
    {
        for ($i = 10; $i < 60; $i += 10) {
            $countPage = $this->productManager->getCountPage($i);
            $this->assertEquals(intval(ceil(50 / $i)), $countPage);
        }

        $countPage = $this->productManager->getCountPage(0);
        $this->assertEquals(0, $countPage);
    }

    /**
     * @throws Exception
     */
    public function testGetProductsWithEmptyFilter()
    {
        $filterDto = new FilterDto();
        $products = $this->productManager->getProducts(0, 50, $filterDto);
        $this->assertEquals($this->getProducts()->toArray(), $products);
    }

    /**
     * @throws Exception
     */
    public function testGetProductsWithQueryFilter()
    {
        $filterDto = new FilterDto();
        $query = '1';
        $filterDto->query = $query;
        $expectedProducts = array_filter($this->getProducts()->toArray(),
            function (Product $product) use ($query): bool {
                return str_contains($product->getName(), $query);
            });
        $products = $this->productManager->getProducts(0, 50, $filterDto);
        $this->assertEqualsCanonicalizing($expectedProducts, $products);
    }

    /**
     * @throws Exception
     */
    public function testGetProductsWithWeightMinFilter()
    {
        $filterDto = new FilterDto();

        $weightMin = 1000;
        $filterDto->weightMin = $weightMin;
        $expectedProducts = array_filter($this->getProducts()->toArray(),
            function (Product $product) use ($weightMin): bool {
                return $product->getWeight() >= $weightMin;
            });
        $products = $this->productManager->getProducts(0, 50, $filterDto);
        $this->assertEqualsCanonicalizing($expectedProducts, $products);
    }

    /**
     * @throws Exception
     */
    public function testGetProductsWithWeightMaxFilter()
    {
        $filterDto = new FilterDto();

        $weightMax = 30000;
        $filterDto->weightMax = $weightMax;
        $expectedProducts = array_filter($this->getProducts()->toArray(),
            function (Product $product) use ($weightMax): bool {
                return $product->getWeight() <= $weightMax;
            });
        $products = $this->productManager->getProducts(0, 50, $filterDto);
        $this->assertEqualsCanonicalizing($expectedProducts, $products);
    }

    /**
     * @throws Exception
     */
    public function testGetProductsWithWeightMinAndMaxFilter()
    {
        $filterDto = new FilterDto();

        $weightMin = 1000;
        $filterDto->weightMin = $weightMin;
        $weightMax = 30000;
        $filterDto->weightMax = $weightMax;
        $expectedProducts = array_filter($this->getProducts()->toArray(),
            function (Product $product) use ($weightMin, $weightMax): bool {
                return ($product->getWeight() >= $weightMin) and ($product->getWeight() <= $weightMax);
            });
        $products = $this->productManager->getProducts(0, 50, $filterDto);
        $this->assertEqualsCanonicalizing($expectedProducts, $products);
    }

    /**
     * @throws Exception
     */
    public function testGetProductsWithCategoryFilter()
    {
        $filterDto = new FilterDto();

        $filterDto->category[] = $this->getCategories()->get(5);
        $products = $this->productManager->getProducts(0, 50, $filterDto);
        $this->assertEmpty($products);

        $categories = [
            $this->getCategories()->get(1),
            $this->getCategories()->get(3),
        ];
        $filterDto->category = $categories;
        $findCategoryIds = array_map(function (Category $category) {
            return $category->getId();
        }, $categories);
        $expectedProducts = array_filter($this->getProducts()->toArray(),
            function (Product $product) use ($findCategoryIds) {
                return in_array($product->getCategory()->getId(), $findCategoryIds, true);
            });
        $products = $this->productManager->getProducts(0, 50, $filterDto);
        $this->assertEqualsCanonicalizing($expectedProducts, $products);
    }

    public function testCreateBatch()
    {
        $batchProductsDto = new ArrayCollection();
        for ($i = 0; $i < 1000; $i++) {
            $batchProductsDto->add((new ProductDto)
                ->setName(sprintf('product dto %d', $i))
                ->setDescription('description')
                ->setWeight(100)
                ->setCategory($this->categories->get(1)));
        }

        $count = $this->productManager->createBatch($batchProductsDto);

        $this->assertEquals(1000, $count);
    }
}
