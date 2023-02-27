<?php

declare(strict_types=1);

namespace UnitTests\Service;

use App\Service\FileUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class FileUploaderTest extends TestCase
{
    private FileUploader $fileUploader;
    private string $path;

    public function setUp(): void
    {
        $this->path = getenv('PATH_TEST');
        $this->fileUploader = new FileUploader($this->path . getenv('UPLOAD_DIR'));
    }

    public function testUpload()
    {
        $tmpfile = $this->path . getenv('FILE_TEST');
        file_put_contents($tmpfile, 'test');
        $symfonyUploadedFile = new SymfonyUploadedFile(
            $tmpfile,
            getenv('FILE_TEST'),
            'text/plan',
            null,
            true);
        $file = $this->fileUploader->upload($symfonyUploadedFile);
        $this->assertFileExists($file);
        $this->assertEquals(getenv('FILE_TEST'), $this->fileUploader->getOriginalFilename());
        unlink($file);
        rmdir($this->path . getenv('UPLOAD_DIR'));
    }
}
