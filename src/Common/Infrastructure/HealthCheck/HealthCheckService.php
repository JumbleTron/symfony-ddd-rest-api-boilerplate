<?php

declare(strict_types=1);

namespace App\Common\Infrastructure\HealthCheck;

use App\Common\Domain\HealthCheckResultAbstract;

class HealthCheckService
{
    public function __construct(private readonly HealthCheckResultAbstract $checkResult)
    {
    }

    public function check(array $healthIndicators): HealthCheckResultAbstract
    {
        foreach ($healthIndicators as $key => $healthIndicator) {
            $this->checkResult->addIndicatorResult($healthIndicator->isHealthy($key));
        }

        return $this->checkResult;
    }
}
