<?php

declare(strict_types=1);

namespace App\Common\Domain;

abstract class HealthIndicatorAbstract
{
    protected function getStatus(
        string $key,
        bool $isHealthy,
        array $params = [],
        string $error = ''
    ): HealthIndicatorResult {
        if ($isHealthy) {
            return new SuccessHealthIndicatorResult($key, true, $params);
        }

        return new FailHealthIndicatorResult($key, false, $error, $params);
    }

    abstract public function isHealthy(string $key): HealthIndicatorResult;
}
