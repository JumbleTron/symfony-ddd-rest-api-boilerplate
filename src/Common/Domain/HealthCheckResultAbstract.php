<?php

declare(strict_types=1);

namespace App\Common\Domain;

use Symfony\Component\HttpFoundation\Response;

abstract class HealthCheckResultAbstract
{
    protected bool $up;

    /**
     * @var HealthIndicatorResult[]
     */
    protected array $indicatorsResult;

    public function __construct()
    {
        $this->indicatorsResult = [];
        $this->up = true;
    }

    public function addIndicatorResult(HealthIndicatorResult $indicatorResult): self
    {
        $this->indicatorsResult[] = $indicatorResult;
        if ($indicatorResult instanceof FailHealthIndicatorResult) {
            $this->up = false;
        }

        return $this;
    }

    abstract public function getResponseResult(): Response;
}
