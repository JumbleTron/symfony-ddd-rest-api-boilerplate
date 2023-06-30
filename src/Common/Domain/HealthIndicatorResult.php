<?php

declare(strict_types=1);

namespace App\Common\Domain;

interface HealthIndicatorResult extends \JsonSerializable
{
    public function jsonSerialize(): array;
}
