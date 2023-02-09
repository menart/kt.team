<?php

namespace App\Import;

use App\Exception\NotSupportedImportFileException;
use App\Import\XML\XMLImport;
use App\Manager\CategoryManager;
use App\Manager\ProductManager;
use App\Service\AsyncService;

class ImportFactory
{
    private CategoryManager $categoryManager;
    private ProductManager $productManager;
    private AsyncService $asyncService;

    /**
     * @param CategoryManager $categoryManager
     * @param ProductManager $productManager
     * @param AsyncService $asyncService
     */
    public function __construct(
        CategoryManager $categoryManager,
        ProductManager  $productManager,
        AsyncService    $asyncService
    )
    {
        $this->categoryManager = $categoryManager;
        $this->productManager = $productManager;
        $this->asyncService = $asyncService;
    }


    public function getInstance($fileName): AbstractImport
    {
        switch ($this->getExtension($fileName)) {
            case 'xml':
                return new XMLImport(
                    $this->categoryManager,
                    $this->productManager,
                    $this->asyncService,
                    $fileName
                );
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