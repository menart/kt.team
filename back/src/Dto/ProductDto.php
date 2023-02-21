<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Category;

/**
 * DTO для entity product
 */
class ProductDto
{
    private string $name;
    private string $description;
    private int $weight;
    private Category $category;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ProductDto
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): ProductDto
    {
        $this->description = $description;
        return $this;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): ProductDto
    {
        $this->weight = $weight;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): ProductDto
    {
        $this->category = $category;
        return $this;
    }

    public function getArray(): array
    {
        return [
            'name'         => $this->name,
            'description'  => $this->description,
            'weight'       => $this->weight,
            'categoryName' => $this->category->getName(),
        ];
    }
}
