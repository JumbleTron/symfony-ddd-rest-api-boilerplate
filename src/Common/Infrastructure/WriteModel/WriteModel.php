<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\WriteModel;

interface WriteModel
{
    public function getValidationGroup(): array;
}
