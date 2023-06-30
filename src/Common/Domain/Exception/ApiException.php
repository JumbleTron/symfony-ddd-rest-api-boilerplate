<?php

declare(strict_types=1);

namespace App\Common\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

final class ApiException extends HttpException
{
    private array $errors;

    public function __construct(
        int $statusCode,
        string $message = null,
        array $errors = [],
        Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        $this->errors = $errors;
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function forBadRequest(array $errors = []): self
    {
        return new static(
            Response::HTTP_BAD_REQUEST,
            Response::$statusTexts[Response::HTTP_BAD_REQUEST],
            $errors
        );
    }

    public static function forUnprocessableEntity(array $errors = []): self
    {
        return new static(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
            $errors
        );
    }

    public static function forFileToBig(float $fileSize, float $allowedFileSize): self
    {
        return new static(
            Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
            Response::$statusTexts[Response::HTTP_REQUEST_ENTITY_TOO_LARGE],
            [
                sprintf(
                    'Uploaded file was to big: %d MB, max upload size is %d MB',
                    $fileSize / 1024 / 1024,
                    $allowedFileSize / 1024 / 1024
                ),
            ]
        );
    }
}
