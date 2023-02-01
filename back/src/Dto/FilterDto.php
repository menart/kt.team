<?php

namespace App\Dto;

class FilterDto
{
    public int $weightMin;
    public int $weightMax;
    /** @var int[] */
    public array $category = [];
    public string $query;

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return json_encode($this->category ?? []);
    }
}