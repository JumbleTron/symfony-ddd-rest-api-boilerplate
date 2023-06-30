<?php

declare(strict_types=1);

namespace App\Common\Application\EventListener;

use App\Common\Domain\ApiErrorResponse;
use App\Common\Domain\Exception\ApiException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTFailureException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class ExceptionListener
{
    public function __construct(private readonly LoggerInterface $logger, private readonly string $environment)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        if ($exception instanceof HandlerFailedException) {
            $exception = $exception->getPrevious();
        }

        if ($exception instanceof UniqueConstraintViolationException) {
            $this->logger->error($exception->getMessage());
            $event->setResponse(
                new ApiErrorResponse('Entity with those values already exists.', Response::HTTP_CONFLICT)
            );
            return;
        }

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }

        if ($exception instanceof JWTFailureException) {
            $statusCode = Response::HTTP_UNAUTHORIZED;
        }

        if ($statusCode >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            $this->logger->error($exception->getMessage(), [$statusCode]);
        }

        if ($statusCode >= Response::HTTP_BAD_REQUEST && $statusCode < Response::HTTP_INTERNAL_SERVER_ERROR) {
            $this->logger->warning($exception->getMessage(), [$statusCode]);
        }

        if ($exception instanceof ApiException) {
            $errors = $exception->getErrors();
        }

        $responseMessage = $exception->getMessage();
        if ($statusCode >= 500 && $this->environment === 'prod') {
            $responseMessage = 'Internal server error';
        }

        $event->setResponse(
            new ApiErrorResponse($responseMessage, $statusCode, $errors ?? [])
        );
    }
}
