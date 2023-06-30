<?php

declare(strict_types=1);

namespace App\Controller;

use App\Common\Domain\Indicator\DoctrineHealthIndicator;
use App\Common\Domain\Indicator\VersionHealthIndicator;
use App\Common\Infrastructure\HealthCheck\HealthCheckService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/status")
 */
class StatusController extends AbstractController
{
    public function __construct(
        private readonly HealthCheckService $checkService,
        private readonly DoctrineHealthIndicator $doctrineHealthIndicator,
        private readonly VersionHealthIndicator $versionHealthIndicator
    ) {
    }

    /**
     * @Route("", name="status_page", methods={"GET"})
     */
    public function indexAction(): Response
    {
        return $this->checkService->check([
            'db' => $this->doctrineHealthIndicator,
            'version' => $this->versionHealthIndicator,
        ])->getResponseResult();
    }

    /**
     * @Route("/full", name="status_full_page", methods={"GET"})
     */
    public function fullAction(): Response
    {
        return $this->checkService->check([
            'db' => $this->doctrineHealthIndicator,
            'version' => $this->versionHealthIndicator,
        ])->getResponseResult();
    }
}
