<?php

namespace UnitTests;

use App\Entity\Category;
use App\Entity\Product;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class AbstractTestCase extends TestCase
{
    /** @var Category[] */
    protected array $categories = [];

    /** @var Product[] */
    protected array $products = [];

    protected EntityRepository $categoryRepository;

    public function setUp(): void
    {
        parent::setUp();
    }

    protected function makeFakeEntityManager(): EntityManagerInterface
    {
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager
            ->method('getRepository')
            ->with(Category::class)
            ->willReturn($this->makeFakeRepository());

        return $entityManager;
    }

    protected function makeFakeRepository(): EntityRepository
    {

        $categoryRepository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $categoryRepository
            ->expects($this->any())
            ->method('findBy')
            ->with()
            ->willReturn($this->getCategories());

        $category = $this->getCategories();
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

    /** @return Category[] */
    protected function categoryFindOneByName($name): array
    {
    }

    private function makeFakeCategores()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->categories[] = (new Category())
                ->setId($i)
                ->setName(sprintf('category %s', $i))
                ->setCreatedAt(new DateTime(sprintf('2022-02-20 22:00:0%s', $i)));
        }
    }

    private function makeFakeProducts()
    {
        $weight = [30, 150, 1200, 30000, 36000];
        $categoryIds = [1, 2, 2, 2, 1];
        for ($i = 0; $i < 5; $i++) {
            $this->product[] = (new Product())
                ->setId($i)
                ->setName(sprintf('product name $s', $i))
                ->setDescription(sprintf('description test $s', $i))
                ->setWeight($weight[$i])
                ->setCategory($this->getCategories()[$categoryIds[$i]])
                ->setCreatedAt(new DateTime(sprintf('2022-02-20 23:00:0%s'), $i));
        }
    }

    /**
     * @return Category[]
     */
    public function getCategories(): array
    {
        if ($this->categories == []) {
            $this->makeFakeCategores();
        }
        return $this->categories;
    }

    /**
     * @return Product[]
     */
    public function getProducts(): array
    {
        if ($this->products == []) {
            $this->makeFakeProducts();
        }
        return $this->products;
    }
}
