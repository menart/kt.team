<?php

declare(strict_types=1);

namespace App\Controller;

use App\Manager\ImportFileManager;
use App\Service\AsyncService;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Контроллер для импорта файлов
 */
#[Route(path: '/import')]
class ImportController extends AbstractController
{
    private const COUNT_LAST_IMPORT_FILE = 10;

    private ImportFileManager $importFileManager;
    private string $error = '';

    public function __construct(ImportFileManager $importFileManager)
    {
        $this->importFileManager = $importFileManager;
    }

    #[Route(path: '', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->getContent();
    }

    /**
     * @throws NotSupportedImportFileException
     * @throws JsonException
     */
    #[Route(path: '', methods: ['POST'])]
    public function uploadFile(
        Request $request,
        FileUploader $fileUploader,
        AsyncService $asyncService
    ): Response {
        $fileUpload = $request->files->get('import-file');
        if (false === empty($fileUpload)) {
            $fileUploadPath = $fileUploader->upload($fileUpload);
            $hash = hash_file('md5', $fileUploadPath);
            if ($this->importFileManager->findImportFileByHash($hash) !== null) {
                $this->error = sprintf('%s уже был загружен ', $fileUploader->getOriginalFilename());
                unlink($fileUploadPath);
            } else {
                $asyncService->publishToExchange(
                    AsyncService::PARSE_DATA_FILE,
                    json_encode(['pathFile' => $fileUploadPath], JSON_THROW_ON_ERROR)
                );
                $this->importFileManager->create($fileUploader->getOriginalFilename(), $hash);
            }
        }

        return $this->getContent();
    }

    private function getContent(): Response
    {
        return $this->render('import.twig', [
            'title'       => 'import',
            'importFiles' => $this->importFileManager->findLastImportFile(self::COUNT_LAST_IMPORT_FILE),
            'error'       => $this->error,
        ]);
    }
}
