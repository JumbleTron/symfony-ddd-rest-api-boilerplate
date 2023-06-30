<?php

declare(strict_types=1);

namespace App\Common\Domain;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiErrorResponse extends JsonResponse
{
    public function __construct(
        string $message,
        int $status = 400,
        array $errors = [],
        array $headers = [],
        bool $json = false
    ) {
        parent::__construct($this->formatBody($message, $status, $errors), $status, $headers, $json);
    }

    private function formatBody(string $message, int $statusCode, array $errors = []): array
    {
        $response = [
            'code' => $statusCode,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}
