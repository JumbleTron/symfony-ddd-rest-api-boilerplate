<?php

declare(strict_types=1);

namespace App\Common\Application\Dto\Sort;

trait SortableTrait
{
    protected array $sort = [];

    public function getSort(): array
    {
        return empty($this->sort) ? $this->getDefaultSort() : $this->sort;
    }

    public function getDefaultSort(): array
    {
        return ['id' => Sortable::SORT_ASC];
    }
}
