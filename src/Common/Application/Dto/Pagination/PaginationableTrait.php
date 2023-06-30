<?php

declare(strict_types=1);

namespace App\Common\Application\Dto\Pagination;

use Symfony\Component\Validator\Constraints as Assert;

trait PaginationableTrait
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(value = 0)
     * @Assert\Positive
     */
    private int $page = 1;

    /**
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(value = 0)
     * @Assert\LessThanOrEqual(value=Paginationable::MAX_PER_PAGE_ITEMS)
     */
    private int $perPage = Paginationable::DEFAULT_PAGE_ITEMS;

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getOffset(): int
    {
        return ($this->getPage() - 1) * $this->getPerPage();
    }
}
