<?php

declare(strict_types=1);

namespace App\Common\Application\Dto\Pagination;

interface Paginationable
{
    public const MAX_PER_PAGE_ITEMS = 30;
    public const DEFAULT_PAGE_ITEMS = 15;

    public function getPage(): int;
    public function getPerPage(): int;
}
