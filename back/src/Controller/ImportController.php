<?php

declare(strict_types=1);

namespace App\Controller;

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
    #[Route(path: '', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('import.twig', [
            'title' => 'import',
        ]);
    }

    /**
     * @throws NotSupportedImportFileException
     * @throws JsonException
     */
    #[Route(path: '', methods: ['POST'])]
    public function uploadFile(Request $request, FileUploader $fileUploader, AsyncService $asyncService): Response
    {
        $fileUpload = $request->files->get('import-file');
        if (false === empty($fileUpload)) {
            $fileUploadPath = $fileUploader->upload($fileUpload);
            $asyncService->publishToExchange(
                AsyncService::PARSE_DATA_FILE,
                json_encode(['pathFile' => $fileUploadPath], JSON_THROW_ON_ERROR)
            );
        }

        return $this->render('import.twig', [
            'title' => 'import',
        ]);
    }
}
