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
    private string $originalFilename;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file): string
    {
        if (file_exists($this->getTargetDirectory()) === false) {
            mkdir($this->getTargetDirectory());
        }
        $this->originalFilename = $file->getClientOriginalName();
        $file->move($this->getTargetDirectory(), $this->originalFilename);

        return $this->getTargetDirectory() . DIRECTORY_SEPARATOR . $this->originalFilename;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    /**
     * @return string
     */
    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }
}
