<?php

declare(strict_types=1);

namespace UnitTests\Manager;

use App\Manager\ImportFileManager;
use Exception;
use UnitTests\AbstractTestCase;

class ImportFileManagerTest extends AbstractTestCase
{
    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->importFileManager = new ImportFileManager($this->makeFakeImportFileManager());
        $this->makeFakeImportFiles();
    }

    public function testFindImportFileByHash()
    {
        for ($i = 1; $i < 15; $i++) {
            $importFile = $this->importFileManager->findImportFileByHash(md5(sprintf('hash %d', $i)));
            $this->assertEquals($i, $importFile->getId());
        }
    }

    public function testFindLastImportFile()
    {
        $importFiles = $this->importFileManager->findLastImportFile(5);
        $this->assertEquals(5, count($importFiles));
        for ($i = array_key_last($importFiles); $i > array_key_first($importFiles) + 1; $i--) {
            $this->assertLessThan($importFiles[$i - 1], $importFiles[$i]);
        }
    }

    public function testCreate()
    {
        $hash = md5('test');
        $name = 'test file';

        $newImportFile = $this->importFileManager->create($name, $hash);

        $testImportFile = $this->importFiles->last();
        $this->assertEquals($testImportFile, $newImportFile);
        $this->assertEquals($hash, $testImportFile->getHash());
        $this->assertEquals($name, $testImportFile->getName());
    }

    public function testUpdateCount()
    {
        $hash = md5('test');
        $name = 'test file';
        $count = 10;

        $newImportFile = $this->importFileManager->create($name, $hash);

        $importFile = $this->importFileManager->updateCount($newImportFile, $count);
        $this->assertEquals($count, $importFile->getCountRecord());
    }

    public function testFinishUpload()
    {
        $hash = md5('test');
        $name = 'test file';


        $newImportFile = $this->importFileManager->create($name, $hash);
        $importFile = $this->importFileManager->finishUpload($newImportFile);

        $this->assertNotNull($importFile->getFinishAt());
    }
}
