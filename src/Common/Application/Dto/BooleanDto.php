<?php

declare(strict_types=1);

namespace App\Common\Application\Dto;

class BooleanDto
{
    public static function create(string|bool|null $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if ($value === 'true' || $value === '1') {
            return true;
        }

        if ($value === 'false' || $value === '0') {
            return false;
        }

        return null;
    }
}
