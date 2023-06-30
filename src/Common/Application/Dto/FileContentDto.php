<?php

declare(strict_types=1);

namespace App\Common\Application\Dto;

class FileContentDto
{
    public function __construct(
        public readonly mixed $fileStream
    ) {
    }
}
