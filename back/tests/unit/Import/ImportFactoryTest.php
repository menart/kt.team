<?php

namespace UnitTests\Import;

use App\Exception\NotSupportedImportFileException;
use App\Import\AbstractImport;
use App\Import\ImportFactory;
use App\Import\XML\XMLImport;
use App\Manager\CategoryManager;
use App\Manager\ProductManager;
use App\Service\AsyncService;
use UnitTests\AbstractTestCase;

class ImportFactoryTest extends AbstractTestCase
{
    private ImportFactory $importFactory;

    protected function setUp(): void
    {
        $categoryManager = \Mockery::mock(CategoryManager::class);
        $productManager = \Mockery::mock(ProductManager::class);
        $asyncService = \Mockery::mock(AsyncService::class);
        $this->importFactory = new ImportFactory($categoryManager, $productManager, $asyncService);
    }

    public function testGetInstance()
    {
        $importFactory = $this->importFactory->getInstance('import.xml');
        $this->assertInstanceOf(AbstractImport::class, $importFactory);
        $this->assertInstanceOf(XMLImport::class, $importFactory);

        $this->expectException(NotSupportedImportFileException::class);
        $this->importFactory->getInstance('import.json');
    }
}
