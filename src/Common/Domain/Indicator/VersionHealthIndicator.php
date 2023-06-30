<?php

declare(strict_types=1);

namespace App\Common\Domain\Indicator;

use App\Common\Domain\HealthIndicatorAbstract;
use App\Common\Domain\HealthIndicatorResult;
use Shivas\VersioningBundle\Service\VersionManagerInterface;

class VersionHealthIndicator extends HealthIndicatorAbstract
{
    public function __construct(private readonly VersionManagerInterface $manager)
    {
    }

    public function isHealthy(string $key): HealthIndicatorResult
    {
        return $this->getStatus($key, true, ['version' => $this->manager->getVersion()->toString()]);
    }
}
