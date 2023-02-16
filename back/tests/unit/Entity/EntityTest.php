<?php

namespace UnitTests\Entity;

use App\Entity\Category;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    public function testGetterAndSetter():void
    {
        $product = new Product();
        $category = new Category();

        $this->assertNull($category->getId());

        $category->setId(1);
        $this->assertEquals(1, $category->getId());

        $category->setCreatedAt();
        $category->setUpdatedAt();

        $this->assertNotNull($category->getCreatedAt());
        $this->assertNotNull($category->getUpdatedAt());

        $nameCategory = 'category name';

        $category->setName($nameCategory);
        $this->assertEquals($nameCategory, $category->getName());

        $this->assertNull($product->getId());

        $product->setId(1);
        $this->assertEquals(1, $product->getId());

        $nameProduct = 'test product';
        $product->setName($nameProduct);
        $this->assertEquals($nameProduct, $product->getName());

        $weight = 100;
        $product->setWeight($weight);
        $this->assertEquals($weight, $product->getWeight());

        $description = 'test description';
        $product->setDescription($description);
        $this->assertEquals($description, $product->getDescription());

        $product->setCreatedAt();
        $product->setUpdatedAt();

        $this->assertNotNull($product->getCreatedAt());
        $this->assertNotNull($product->getUpdatedAt());

        $product->setCategory($category);
        $this->assertEquals($nameCategory, $product->getCategory()->getName());
    }
}
