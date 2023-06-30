<?php

declare(strict_types=1);

namespace App\Common\Application\Dto\Search;

use Symfony\Component\Validator\Constraints as Assert;

trait SearchableByCustomerIdTrait
{
    /**
     * @Assert\Uuid()
     */
    private ?string $customerId = null;

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }
}
