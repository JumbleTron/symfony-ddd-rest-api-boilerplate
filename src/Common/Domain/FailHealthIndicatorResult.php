<?php

declare(strict_types=1);

namespace App\Common\Domain;

class FailHealthIndicatorResult implements HealthIndicatorResult
{
    public function __construct(
        private readonly string $key,
        private readonly bool $status,
        private readonly string $error,
        private readonly array $details = []
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            $this->key => [
                'status' => $this->status === true ? 'up' : 'down',
                'details' => $this->details,
                'error' => [
                    'message' => $this->error,
                ],
            ],
        ];
    }
}
