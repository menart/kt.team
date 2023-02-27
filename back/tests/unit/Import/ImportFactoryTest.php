<?php

declare(strict_types=1);

namespace UnitTests\Import;

use App\Entity\ImportFile;
use App\Exception\NotSupportedImportFileException;
use App\Import\AbstractImport;
use App\Import\ImportFactory;
use App\Import\XML\XMLImport;
use App\Manager\CategoryManager;
use App\Manager\ImportFileManager;
use App\Manager\ProductManager;
use Mockery;
use UnitTests\AbstractTestCase;

class ImportFactoryTest extends AbstractTestCase
{
    private ImportFactory $importFactory;
    protected ImportFileManager $importFileManager;

    public function setUp(): void
    {
        $categoryManager = Mockery::mock(CategoryManager::class);
        $productManager = Mockery::mock(ProductManager::class);
        $this->importFileManager = $this->getMockBuilder(ImportFileManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->importFileManager->method('getHash')->willReturn('hash');
        $this->importFileManager->method('findImportFileByHash')->willReturn(new ImportFile());
        $this->importFactory = new ImportFactory($categoryManager, $productManager, $this->importFileManager);
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
