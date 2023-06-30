<?php

declare(strict_types=1);

namespace App\Common\Application\EventListener;

use App\Common\Application\Validator\WriteModelValidatorInterface;
use App\Common\Domain\Exception\ApiException;
use App\Common\Infrastructure\WriteModel\FileWriteModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use TypeError;

class RequestToFileWriteModelResolver implements ArgumentValueResolverInterface
{
    public const MAX_FILE_SIZE = 1024 * 1024 * 10; //10MB

    public function __construct(
        private readonly WriteModelValidatorInterface $writeModelValidator,
        private readonly DenormalizerInterface $denormalizer,
        private readonly LoggerInterface $logger,
    ) {
    }

    //@phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_a($argument->getType(), FileWriteModel::class, true);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $content = $request->request->all();

        try {
            /** @var FileWriteModel $writeModel */
            $writeModel = $this->denormalizer->denormalize(
                $content,
                $argument->getType(),
                'array',
                [
                    DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true,
                ]
            );
        } catch (TypeError $e) {
            $this->logger->debug($e->getMessage());
            throw ApiException::forBadRequest();
        }

        $contentLength = (float)$request->headers->get('CONTENT_LENGTH');
        $file = $request->files->get('file');

        if ($contentLength <= 0) {
            throw ApiException::forBadRequest(['Content-Length header not set']);
        }

        if (!$file instanceof File && $contentLength > self::MAX_FILE_SIZE) {
            throw ApiException::forFileToBig($contentLength, self::MAX_FILE_SIZE);
        }

        if ($file instanceof File) {
            $writeModel->setFile($file);
        }

        $this->writeModelValidator->validate($request, $writeModel);

        yield $writeModel;
    }
}
