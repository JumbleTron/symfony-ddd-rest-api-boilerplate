<?php

declare(strict_types=1);

namespace App\Common\Domain;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HealthCheckJsonResult extends HealthCheckResultAbstract
{
    public function getResponseResult(): Response
    {
        return new JsonResponse(
            [
                'status' => $this->up === true ? 'up' : 'down',
                'checks' => $this->indicatorsResult,
            ],
            $this->up === true ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
