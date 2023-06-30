<?php

declare(strict_types=1);

namespace App\Common\Application\Dto\Search;

interface Searchable
{
    public function getSearch(): ?string;
}
