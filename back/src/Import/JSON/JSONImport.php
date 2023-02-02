<?php

namespace App\Import\JSON;

use App\Dto\ProductDto;
use App\Import\AbstractImport;

class JSONImport extends AbstractImport
{
    public function parse(): int
    {
        if (file_exists($this->fileName) === false) {
            return 0;
        }

        $countParse = 0;
        $fileResource = fopen($this->fileName, 'r');
        while (feof($fileResource) === false) {
            $jsonLine = fgets($fileResource, 10 * 1024);
            if (strlen(trim($jsonLine)) > 0) {
                /** @var ProductDto $productDto */
                $productDto = json_decode($jsonLine, true);
                $this->saveProduct(
                    $productDto['name'],
                    $productDto['description'],
                    $productDto['weight'],
                    $productDto['categoryName']
                );
                $countParse++;
            }
        }
        unlink($this->fileName);
        $this->saveBatch();
        return $countParse;
    }
}