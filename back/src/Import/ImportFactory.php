<?php

namespace App\Import;

use App\Exception\NotSupportedImportFileException;
use App\Import\XML\XMLImport;
use App\Manager\CategoryManager;
use App\Manager\ProductManager;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Cache\CacheItemPoolInterface;

class ImportFactory
{
    private CategoryManager $categoryManager;
    private ProductManager $productManager;
    protected CacheItemPoolInterface $cacheItemPool;

    /**
     * @param CategoryManager $categoryManager
     * @param ProductManager $productManager
     * @param CacheItemPoolInterface $cacheItemPool
     */
    public function __construct(
        CategoryManager        $categoryManager,
        ProductManager         $productManager,
        CacheItemPoolInterface $cacheItemPool
    )
    {
        $this->categoryManager = $categoryManager;
        $this->productManager = $productManager;
        $this->cacheItemPool = $cacheItemPool;
        $this->categories = new ArrayCollection();
        $this->products = new ArrayCollection();
    }


    public function getInstance($fileName): AbstractImport
    {
        switch ($this->getExtension($fileName)){
            case 'xml':
                return new XMLImport($this->categoryManager, $this->productManager, $this->cacheItemPool);
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