<?php

declare(strict_types=1);

namespace App\Common\Domain\Enum;

enum Search: string
{
    case SEARCH_TEXT = 'SEARCH_TEXT';
    case SEARCH_DATE = 'SEARCH_DATE';
    case SEARCH_NUMERIC = 'SEARCH_NUMERIC';
    case SEARCH_SMALLINT = 'SEARCH_SMALLINT';
    case SEARCH_UUID = 'SEARCH_UUID';
    case SEARCH_BOOLEAN = 'SEARCH_BOOLEAN';
    case SEARCH_DATE_DAY = 'SEARCH_DATE_DAY';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
