<?php

namespace App\Controller;

use App\Exception\NotSupportedImportFileException;
use App\Service\AsyncService;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     */
    #[Route(path: '', methods: ['POST'])]
    public function uploadFile(Request $request, FileUploader $fileUploader, AsyncService $asyncService): Response
    {
        $fileUpload = $request->files->get('import-file');
        if (empty($fileUpload) === false) {
            $fileUploadPath = $fileUploader->upload($fileUpload);
            $asyncService->publishToExchange(
                AsyncService::PARSE_DATA_FILE,
                json_encode(['pathFile' => $fileUploadPath], JSON_THROW_ON_ERROR)
            );
        }
        return $this->render('import.twig', [
            'title' => 'import'
        ]);
    }

}