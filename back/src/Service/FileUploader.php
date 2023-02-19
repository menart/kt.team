<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Для загрузки файла
 */
class FileUploader
{
    private string $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file): string
    {
        if (file_exists($this->getTargetDirectory()) === false) {
            mkdir($this->getTargetDirectory());
        }
        $originalFilename = $file->getClientOriginalName();
        $file->move($this->getTargetDirectory(), $originalFilename);

        return $this->getTargetDirectory() . DIRECTORY_SEPARATOR . $originalFilename;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
