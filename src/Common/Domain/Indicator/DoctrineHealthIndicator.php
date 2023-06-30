<?php

declare(strict_types=1);

namespace App\Common\Domain\Indicator;

use App\Common\Domain\HealthIndicatorAbstract;
use App\Common\Domain\HealthIndicatorResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class DoctrineHealthIndicator extends HealthIndicatorAbstract
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function isHealthy(string $key): HealthIndicatorResult
    {
        try {
            $this->connection->executeQuery('SELECT 1');
            return $this->getStatus($key, true);
        } catch (Exception $e) {
            return $this->getStatus($key, false, [], $e->getMessage());
        }
    }
}
