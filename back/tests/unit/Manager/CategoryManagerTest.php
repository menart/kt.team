<?php

namespace UnitTests\Manager;

use App\Manager\CategoryManager;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use UnitTests\AbstractTestCase;

class CategoryManagerTest extends AbstractTestCase
{

    public CategoryManager $categoryManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->makeFakeRepository();
        $this->categoryManager = new CategoryManager($this->makeFakeEntityManager());
    }

    public function testGetAll()
    {
        $returnCategory = $this->categoryManager->getAll();
        $this->assertEquals($this->categories, $returnCategory);
    }

    public function testFindExistName()
    {
        for ($i = 0; $i < 5; $i++){
            $findCategory = $this->categoryManager->getOrCreate(sprintf('category %s', $i));
            $this->assertEquals($i, $findCategory->getId());
        }
    }
}
