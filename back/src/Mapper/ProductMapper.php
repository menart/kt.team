<?php

namespace App\Mapper;

use App\Dto\ProductDto;
use App\Entity\Product;

class ProductMapper
{
    public static function toEntity(ProductDto $dto): Product
    {
        $product = new Product();
        $product->setName($dto->getName());
        $product->setDescription($dto->getDescription());
        $product->setWeight($dto->getWeight());
        $product->setCategory($dto->getCategory());
        return $product;
    }
}