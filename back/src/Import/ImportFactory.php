<?php

namespace App\Import;

use App\Exception\NotSupportedImportFileException;
use App\Import\XML\XMLImport;
use App\Import\JSON\JSONImport;
use App\Manager\CategoryManager;
use App\Manager\ProductManager;
use App\Service\AsyncService;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Cache\CacheItemPoolInterface;

class ImportFactory
{
    private CategoryManager $categoryManager;
    private ProductManager $productManager;
    private CacheItemPoolInterface $cacheItemPool;
    private ExportFactory $exportFactory;
    private AsyncService $asyncService;

    /**
     * @param CategoryManager $categoryManager
     * @param ProductManager $productManager
     * @param CacheItemPoolInterface $cacheItemPool
     * @param ExportFactory $exportFactory
     * @param AsyncService $asyncService
     */
    public function __construct(
        CategoryManager        $categoryManager,
        ProductManager         $productManager,
        CacheItemPoolInterface $cacheItemPool,
        ExportFactory          $exportFactory,
        AsyncService           $asyncService
    )
    {
        $this->categoryManager = $categoryManager;
        $this->productManager = $productManager;
        $this->cacheItemPool = $cacheItemPool;
        $this->exportFactory = $exportFactory;
        $this->asyncService = $asyncService;
        $this->categories = new ArrayCollection();
        $this->products = new ArrayCollection();
    }


    public function getInstance($fileName): AbstractImport
    {
        switch ($this->getExtension($fileName)) {
            case 'xml':
                return new XMLImport(
                    $this->categoryManager,
                    $this->productManager,
                    $this->cacheItemPool,
                    $this->exportFactory,
                    $this->asyncService,
                    $fileName
                );
            case 'json':
                return new JSONImport(
                    $this->categoryManager,
                    $this->productManager,
                    $this->cacheItemPool,
                    $this->exportFactory,
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