<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * DTO список полученной информации о фильтрации с фронта
 */
class FilterDto
{
    public int $weightMin = 0;
    public int $weightMax = 0;
    /** @var int[] */
    public array $category = [];
    public string $query = '';

    public function getCategory(): string
    {
        return json_encode($this->category ?? []);
    }
}
