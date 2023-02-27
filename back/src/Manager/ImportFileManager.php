<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\ImportFile;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Управляет информацией о загруженных файлах
 */
class ImportFileManager
{
    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(ImportFile::class);
    }

    public function create(string $name, string $hash): ImportFile
    {
        $importFile = new ImportFile();
        $importFile->setName($name)
            ->setHash($hash)
            ->setUploadAt();
        $this->entityManager->persist($importFile);
        $this->entityManager->flush();
        return $importFile;
    }

    public function findImportFileByHash(string $hash): ?ImportFile
    {
        return $this->repository->findOneBy(['hash' => $hash]);
    }

    public function getHash(string $filePath): string
    {
        if (!file_exists($filePath)) {
            return '';
        }
        return md5_file($filePath);
    }

    /**
     * @param  int $count
     * @return ImportFile[]
     */
    public function findLastImportFile(int $count): array
    {
        return $this->repository->findBy([], ['uploadAt' => 'DESC'], $count, 0);
    }

    public function updateCount(ImportFile $importFile, int $count): ImportFile
    {
        $this->entityManager->find(ImportFile::class, $importFile->getId());
        $importFile->setCountRecord($count);
        $this->entityManager->flush();
        return $importFile;
    }

    public function finishUpload(ImportFile $importFile): ImportFile
    {
        $this->entityManager->find(ImportFile::class, $importFile->getId());
        $importFile->setFinishAt();
        $this->entityManager->flush();
        return $importFile;
    }
}
