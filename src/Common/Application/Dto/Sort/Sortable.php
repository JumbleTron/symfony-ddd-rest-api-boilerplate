<?php

declare(strict_types=1);

namespace App\Common\Application\Dto\Sort;

interface Sortable
{
    public const SORT_ASC = 'asc';
    public const SORT_DESC = 'desc';

    public const DIRECTIONS = [self::SORT_ASC, self::SORT_DESC];

    public function getSort(): array;
    public function getDefaultSort(): array;
}
