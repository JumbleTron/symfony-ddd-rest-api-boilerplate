<?php

declare(strict_types=1);

namespace App\Common\Application\Dto\Search;

use Symfony\Component\Validator\Constraints as Assert;

trait SearchableByBookingIdTrait
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    private string $bookingId;

    public function getBookingId(): string
    {
        return $this->bookingId;
    }
}
