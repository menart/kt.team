<?php

namespace UnitTests\Service;

use App\Service\FileUploader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use PHPUnit\Framework\TestCase;

class FileUploaderTest extends TestCase
{

    const PATH = '/www/tests/unit/fixture/';
    const FILE_TEST = 'test.xml';
    const UPLOAD_DIR = self::PATH . 'upload/';

    private FileUploader $fileUploader;

    public function setUp(): void
    {
        $this->fileUploader = new FileUploader(self::UPLOAD_DIR);
    }

    public function testUpload()
    {
        $tmpfile = self::PATH . self::FILE_TEST;
        file_put_contents($tmpfile, 'test');
        $symfonyUploadedFile = new SymfonyUploadedFile(
            $tmpfile,
            self::FILE_TEST,
            'text/plan',
            null,
            true);
        $file = $this->fileUploader->upload($symfonyUploadedFile);
        $this->assertFileExists($file);
        unlink($file);
        rmdir(self::UPLOAD_DIR);
    }
}
