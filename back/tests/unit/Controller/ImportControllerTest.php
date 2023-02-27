<?php

declare(strict_types=1);

namespace UnitTests\Controller;

use App\Controller\ImportController;
use App\Entity\ImportFile;
use App\Manager\ImportFileManager;
use App\Service\AsyncService;
use App\Service\FileUploader;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ImportControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        foreach (['GET', 'POST'] as $method) {
            $client->request($method, '/import');
            $this->assertResponseIsSuccessful();
            $this->assertPageTitleSame('kt.tream: import');
            $this->assertSelectorExists('#import-file');
            $this->assertSelectorTextContains('button[type="submit"]', 'Загрузить');
        }
    }

    /**
     * @throws JsonException
     */
    public function testUploadFile()
    {
        $fakePath = __FILE__;

        $symfonyUploadedFile = new SymfonyUploadedFile(
            $fakePath,
            getenv('FILE_TEST'),
            'text/plan',
            null,
            true);

        $fileUploader = $this
            ->getMockBuilder(FileUploader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileUploader
            ->expects($this->once())
            ->method('upload')
            ->with($symfonyUploadedFile)
            ->willReturn($fakePath);

        $asyncService = $this
            ->getMockBuilder(AsyncService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $asyncService
            ->expects($this->once())
            ->method('publishToExchange')
            ->with(AsyncService::PARSE_DATA_FILE,
                json_encode(['pathFile' => $fakePath], JSON_THROW_ON_ERROR));

        $importFileManager =
            $this->getMockBuilder(ImportFileManager::class)
                ->disableOriginalConstructor()
                ->getMock();

        $importController = $this
            ->getMockBuilder(ImportController::class)
            ->setConstructorArgs([$importFileManager])
            ->onlyMethods(['render'])
            ->getMock();

        $request = new Request();
        $request->files->set('import-file', $symfonyUploadedFile);

        $importFileManager->expects($this->once())->method('findImportFileByHash');
        $importFileManager->expects($this->once())->method('create');

        $importController->uploadFile($request, $fileUploader, $asyncService);
    }

    public function testUploadExistsFile()
    {
        $fakePath = getenv('PATH_TEST') . 'tests';
        file_put_contents($fakePath, 'test');
        $symfonyUploadedFile = new SymfonyUploadedFile(
            $fakePath,
            getenv('FILE_TEST'),
            'text/plan',
            null,
            true);

        $fileUploader = $this
            ->getMockBuilder(FileUploader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileUploader
            ->expects($this->once())
            ->method('upload')
            ->with($symfonyUploadedFile)
            ->willReturn($fakePath);

        $asyncService = $this
            ->getMockBuilder(AsyncService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $importFileManager =
            $this->getMockBuilder(ImportFileManager::class)
                ->disableOriginalConstructor()
                ->getMock();

        $importFileManager->method('findImportFileByHash')->willReturn(new ImportFile());

        $importFileManager->expects($this->once())->method('findImportFileByHash');
        $importFileManager->expects($this->never())->method('create');
        $asyncService->expects($this->never())->method('publishToExchange');

        $importController = $this
            ->getMockBuilder(ImportController::class)
            ->setConstructorArgs([$importFileManager])
            ->onlyMethods(['render'])
            ->getMock();

        $request = new Request();
        $request->files->set('import-file', $symfonyUploadedFile);

        $importController->uploadFile($request, $fileUploader, $asyncService);
        $this->assertFileDoesNotExist($fakePath);
    }
}
