<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\ReadModel;

class CollectionReadModel
{
    public function __construct(
        private readonly int $totalItems,
        private readonly int $perPage,
        private readonly array $data
    ) {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }
}
