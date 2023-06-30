<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\File;

use App\Common\Application\Dto\FileDto;
use App\Common\Infrastructure\WriteModel\FileWriteModel;

interface FileUploader
{
    public function upload(FileWriteModel $writeModel, ?string $fileName = null): FileDto;
    public function read(FileDto $fileDto): mixed;
    public function readFile(FileDto $fileDto): string;
    public function write(string $fileName, string $fileExtension, mixed $content): FileDto;
    public function getFileSize(string $filePath): int;
    public function writeStream(string $fileName, string $fileExtension, mixed $content): FileDto;
}
