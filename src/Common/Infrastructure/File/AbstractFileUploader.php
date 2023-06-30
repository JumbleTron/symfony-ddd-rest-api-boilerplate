<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\File;

use App\Common\Application\Dto\FileDto;
use App\Common\Infrastructure\WriteModel\FileWriteModel;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\String\Slugger\SluggerInterface;

abstract class AbstractFileUploader implements FileUploader
{
    public function __construct(
        private FilesystemOperator $defaultStorage,
        private SluggerInterface $slugger
    ) {
    }

    public function upload(FileWriteModel $writeModel, ?string $fileName = null): FileDto
    {
        $file = $writeModel->getFile();
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->guessExtension();
        $filePath = $fileName ?? $this->generateUniqueName($originalFilename, $extension);
        $path = sprintf('%s/%s', $this->getDir(), $filePath);
        $this->defaultStorage->write($path, $file->getContent());

        return new FileDto(
            $originalFilename,
            $filePath,
            $extension,
            $file->getSize() ? $file->getSize() : null
        );
    }

    public function read(FileDto $fileDto): mixed
    {
        return $this->defaultStorage->readStream(sprintf('%s/%s', $this->getDir(), $fileDto->path));
    }

    public function readFile(FileDto $fileDto): string
    {
        return $this->defaultStorage->read(sprintf('%s/%s', $this->getDir(), $fileDto->path));
    }

    public function write(string $fileName, string $fileExtension, mixed $content): FileDto
    {
        $filePath = $this->generateUniqueName($fileName, $fileExtension);
        $this->defaultStorage->write(sprintf('%s/%s', $this->getDir(), $filePath), $content);

        $fileSize = $this->defaultStorage->fileSize(sprintf('%s/%s', $this->getDir(), $filePath));

        return new FileDto($fileName, $filePath, $fileExtension, $fileSize);
    }

    public function getFileSize(string $filePath): int
    {
        return $this->defaultStorage->fileSize(sprintf('%s/%s', $this->getDir(), $filePath));
    }

    public function writeStream(string $fileName, string $fileExtension, mixed $content): FileDto
    {
        $filePath = $this->generateUniqueName($fileName, $fileExtension);
        $this->defaultStorage->writeStream(sprintf('%s/%s', $this->getDir(), $filePath), $content);

        return new FileDto($fileName, $filePath, $fileExtension, null);
    }

    private function generateUniqueName(string $originalName, string $extension): string
    {
        return sprintf("%s-%s.%s", $this->slugger->slug($originalName), uniqid(), $extension);
    }

    abstract protected function getDir(): string;
}
