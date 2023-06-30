<?php

declare(strict_types=1);

namespace App\Common\Application\Dto\Search;

trait SearchableTrait
{
    private ?string $search = null;

    public function getSearch(): ?string
    {
        return $this->search ? strtolower($this->search) : null;
    }
}
