<?php

namespace UnitTests\Manager;

use App\Manager\CategoryManager;
use Exception;
use UnitTests\AbstractTestCase;

class CategoryManagerTest extends AbstractTestCase
{

    public CategoryManager $categoryManager;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->categoryManager = new CategoryManager($this->makeFakeCategoryManager());
    }

    public function testGetAll()
    {
        $returnCategory = $this->categoryManager->getAll();
        $this->assertEquals($this->categories->toArray(), $returnCategory);
    }

    public function testFindExistName()
    {
        for ($i = 1; $i < 6; $i++){
            $findCategory = $this->categoryManager->getOrCreate(sprintf('category %d', $i));
            $this->assertEquals($i, $findCategory->getId());
        }
    }

    public function testNotFoundAndCreate()
    {
        $findCategory = $this->categoryManager->getOrCreate('new category');
        $this->assertEquals(6, $findCategory->getId());
    }
}
