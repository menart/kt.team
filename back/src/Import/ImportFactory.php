<?php

namespace App\Import;

use App\Exception\NotSupportedImportFileException;
use App\Import\XML\XMLImport;
use App\Manager\CategoryManager;
use App\Manager\ProductManager;

class ImportFactory
{
    private CategoryManager $categoryManager;
    private ProductManager $productManager;

    /**
     * @param CategoryManager $categoryManager
     * @param ProductManager $productManager
     */
    public function __construct(CategoryManager $categoryManager, ProductManager $productManager)
    {
        $this->categoryManager = $categoryManager;
        $this->productManager = $productManager;
    }


    public function getInstance($fileName): AbstractImport
    {
        switch ($this->getExtension($fileName)){
            case 'xml':
                return new XMLImport($this->categoryManager, $this->productManager);
            default:
                throw new NotSupportedImportFileException();
        }
    }

    private function getExtension(string $filename): string
    {
        $array = explode(".", $filename);
        return strtolower(array_pop($array));
    }
}