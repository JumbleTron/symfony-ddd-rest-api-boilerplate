<?php

declare(strict_types=1);

namespace App\Common\Application\Dto;

class FileDto
{
    public function __construct(
        public readonly string $fileName,
        public readonly string $path,
        public readonly ?string $extension = null,
        public readonly ?int $size = null
    ) {
    }
}
