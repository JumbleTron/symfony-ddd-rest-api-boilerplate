<?php

declare(strict_types=1);

namespace App\Common\Application\EventListener;

use App\Common\Application\Validator\WriteModelValidatorInterface;
use App\Common\Domain\Exception\ApiException;
use App\Common\Infrastructure\WriteModel\AbstractWriteModel;
use App\Common\Infrastructure\WriteModel\WriteModel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use TypeError;

class RequestToWriteModelResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly WriteModelValidatorInterface $validator,
        private readonly LoggerInterface $logger
    ) {
    }

    //@phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_a($argument->getType(), WriteModel::class, true);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $content = $request->getContent() !== '' ? $request->getContent() : '{}';
        try {
            /** @var AbstractWriteModel $writeModel */
            $writeModel = $this->serializer->deserialize(
                $content,
                $argument->getType(),
                'json',
                [
                    DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true,
                ]
            );
        } catch (TypeError $e) {
            $this->logger->debug($e->getMessage());
            throw ApiException::forBadRequest([]);
        }

        $this->validator->validate($request, $writeModel);
        $writeModel->setPassedField(array_keys(json_decode($content, true)));

        yield $writeModel;
    }
}
