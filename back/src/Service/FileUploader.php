<?php

namespace App\Service;

use App\Import\ImportFactory;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

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