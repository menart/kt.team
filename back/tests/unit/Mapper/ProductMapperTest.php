<?php

namespace UnitTests\Mapper;

use App\Dto\ProductDto;
use App\Entity\Category;
use App\Entity\Product;
use App\Mapper\ProductMapper;
use PHPUnit\Framework\TestCase;

class ProductMapperTest extends TestCase
{

    public function testToEntity()
    {
        $name = 'test name';
        $weight = 100;
        $description = 'test description';
        $category = new Category();
        $nameCategory = 'test category';
        $category->setName($nameCategory);

        $productDto = new ProductDto();
        $productDto->setName($name);
        $productDto->setWeight($weight);
        $productDto->setDescription($description);
        $productDto->setCategory($category);

        $product = ProductMapper::toEntity($productDto);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($name, $product->getName());
        $this->assertEquals($weight, $product->getWeight());
        $this->assertEquals($description, $product->getDescription());
        $this->assertEquals($nameCategory, $product->getCategory()->getName());

        $arrayDto = [
            'name' => $name,
            'description' => $description,
            'weight' => $weight,
            'categoryName' => $category->getName(),
        ];
        $this->assertEquals($arrayDto, $productDto->getArray());
    }
}
