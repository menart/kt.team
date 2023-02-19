<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Dto\ProductDto;
use App\Entity\Product;

/**
 * Перевод из Dto в product entity
 */
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
