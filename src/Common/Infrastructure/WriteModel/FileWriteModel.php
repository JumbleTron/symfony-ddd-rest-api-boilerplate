<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\WriteModel;

use Symfony\Component\HttpFoundation\File\File;

interface FileWriteModel
{
    public function getFile(): File;

    public function setFile(File $file): void;

    public function getValidationGroup(): array;
}
