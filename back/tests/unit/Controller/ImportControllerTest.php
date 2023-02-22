<?php

namespace UnitTests\Controller;

use App\Controller\ImportController;
use App\Service\AsyncService;
use App\Service\FileUploader;
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
     * @throws \JsonException
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

        $importController = $this
            ->getMockBuilder(ImportController::class)
            ->onlyMethods(['render'])
            ->getMock();

        $request = new Request();
        $request->files->set('import-file', $symfonyUploadedFile);

        $importController->uploadFile($request, $fileUploader, $asyncService);
    }
}
